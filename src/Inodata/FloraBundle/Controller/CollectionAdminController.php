<?php
namespace Inodata\FloraBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Inodata\FloraBundle\Entity\Product;
use Inodata\FloraBundle\Entity\Order;
use Inodata\FloraBundle\Entity\OrderPayment;
use Inodata\FloraBundle\Form\Type\CollectionType;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class CollectionAdminController extends Controller{
	
	public function listAction()
	{
		if (false === $this->admin->isGranted('LIST')) {
			throw new AccessDeniedException();
		}
	
		if ($this->getRequest()->get('view')){
			$this->setListView($this->getRequest()->get('view'));
		}
	
		$this->setFilters($this->get('request')->get('filter'));
	
		$datagrid = $this->admin->getDatagrid();
		$formView = $datagrid->getForm()->createView();
	
		$order = new Order();
		$collectionFormView = $this->createForm(new CollectionType(), $order)->createView();
	
		// set the theme for the current Admin Form
		$this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());
	
		$collectors = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Employee')
			->findByJobPosition('Collector');
		
		$firstTab = 0;
		$lastTab = 0;
	
		if($collectors){
			$firstTab = $collectors[0]->getId();
			$lastTab = $collectors[count($collectors)-1]->getId();
		}
	
	
		isset($collectors[0])==true? $firstCollector = $collectors[0]->getId():$firstCollector=0;
	
		$render = $this->render($this->admin->getTemplate('list'), array(
				'action'   => 'list',
				'form'     => $formView,
				'collection_form' => $collectionFormView,
				'datagrid' => $datagrid,
				'csrf_token' => $this->getCsrfToken('sonata.batch'),
				'collectors' => $collectors,
				'first_tab' => $firstTab,
				'last_tab' => $lastTab,
				'selected_collector' => $this->getSelectedCollector($firstCollector),
				'list_view' => $this->getListeView(),
				'orders'	=> $this->getCollectorOrders($this->getSelectedCollector($firstCollector)),
				'payments' => $this->getDespositsAndCommissionByCollector($this->getSelectedCollector())
		));
	
		return $render;
	}
	
	private function getCollectorOrders($collectorId)
	{
		$status = $this->getSelectedStatus();
		
		if (!$status){
			$status = "o.status='partiallypayment' OR o.status = 'closed'";
		}else{
			$status="o.status='".$status."'";
		}
		
		$orders = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Order')
			->createQueryBuilder('o')
			->where($status)
			->andWhere('o.collector=:collector')
			->andWhere('o.collectionDate>=:dateStart AND o.collectionDate<=:dateEnd')
			->setParameters(array('collector'=>$collectorId, 'dateStart'=>$this->getDateStart(), 'dateEnd'=>$this->getDateEnd()))
			->getQuery()
			->getResult();
		
		return $orders;
	}
	
	private function getDespositsAndCommissionByCollector($id)
	{
		$orders = $this->getCollectorOrders($id);
		
		$totalOrders = 0;
		foreach ($orders as $order){
			$totalOrders+=$order->getOrderTotals();
		}
		
		$earning = $this->container->getParameter('collector_commission');
		$totalcommission = ($totalOrders * $earning);
		
		return array("payments"=>$totalOrders, "commission"=>$totalcommission);
	}
	
	
	public function loadOrdersByCollectorAction($id)
	{
		$this->setSelectedCollector($id);
		$id = $this->getSelectedCollector();
		
		$orders = $this->getCollectorOrders($id);
		$response = $this->renderView('InodataFloraBundle:Collection:_list_item.html.twig',
				array('orders' => $orders));
		
		$paymentsAndCommision = $this->getDespositsAndCommissionByCollector($id);
		 
		return new Response(json_encode(array('orders'=>$response,
				'id'=>$id, 'payments'=>$paymentsAndCommision['payments'],
				'commission' => $paymentsAndCommision['commission'])));
	}
	
	public function addOrderToCollectorAction()
	{
		/* Obtiene los valores por post */
		$collectorId = $this->get('request')->get('collector_id');
		$orderId = $this->get('request')->get('order_id');
	
		/* Encuentra el Messenger */
		$collector = $this->getDoctrine()->getRepository('InodataFloraBundle:Employee')
			->find( $collectorId );
	
		$em = $this->getDoctrine()->getManager();
		 
		$order = $this->getDoctrine()->getRepository('InodataFloraBundle:Order')
			->find( $orderId );
	
		if( $order != null ){
			$order->setStatus('partiallypayment');
			$order->setCollector($collector);
			$order->setCollectionDate(new \DateTime("NOW"));
			 
			$em->persist($order);
			$em->flush();
		}
		 
		$row = $this->renderView('InodataFloraBundle:Collection:_list_item.html.twig',
				array('orders' => array(0=>$order)));
		 
		//Cargar View con estos datos
		$ordersDelivered = $this->getOrderOptionByStatus("delivered");
		 
		$orderOptions = $this->renderView('InodataFloraBundle:Distribution:_order_option.html.twig',
				array('orders' => $ordersDelivered));
		
		$paymentsAndCommision = $this->getDespositsAndCommissionByCollector($collectorId);
	
		return new Response(json_encode(array('order'=>$row,
				'id'=>$collectorId, 'orderOptions'=>$orderOptions,
				'payments'=>$paymentsAndCommision['payments'],
				'commission' => $paymentsAndCommision['commission'])));
	}
	
	private function getOrderOptionByStatus($status)
	{
		$ordersDelivered = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Order')
			->findByStatus($status);
		
		return $ordersDelivered;
	}
	
	public function removeOrderAction($orderId)
	{
		$em = $this->getDoctrine()->getManager();
		$order = $em->getRepository('InodataFloraBundle:Order')
			->find($orderId);
		
		$success = false;
		if ($order){
			$order->setCollector(null);
			$order->setStatus('delivered');
			$order->setCollectionDate(null);
			$em->persist($order);
			$em->flush();
			$success = true;
		}
		
		if ($this->isXmlHttpRequest()){
			$orderOptions = $this->renderView('InodataFloraBundle:Distribution:_order_option.html.twig',
					array('orders' => $this->getOrderOptionByStatus("delivered")));
			
			$paymentsAndCommision = $this
				->getDespositsAndCommissionByCollector($this->getSelectedCollector());
					
			return new Response(json_encode(array(
					"success"=>$success, 
					"orderOptions"=>$orderOptions,
					'payments'=>$paymentsAndCommision['payments'],
					'commission' => $paymentsAndCommision['commission']
				)));
		}
		
		return new RedirectResponse($this->generateUrl('collection_list'));
	}
	
	//Messenger edit in place
	public function editInPlaceAction()
	{
		$employeeId = $this->get('request')->get('pk');
		$employeeAttr = $this->get('request')->get('name');
		$value =  $this->get('request')->get('value');
	
		$em = $this->getDoctrine()->getManager();
		$employee = $em->getRepository('InodataFloraBundle:Employee')
		->find($employeeId);
		 
		switch($employeeAttr){
			case 'name':
				$employee->setName($value);
				break;
			case 'lastname':
				$employee->setLastname($value);
				break;
			case 'phone':
				$employee->setPhone($value);
				break;
		};
	
		$em->persist($employee);
	
		$em->flush();
		$em->clear();
	
		return new Response("success");
	}
	
	public function paymentsOrderDetailsAction($orderId){
		
		$order = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Order')
			->find($orderId);
		
		$lastPayments = $order->getLastOrderPayments();
		$actualPayments = $order->getActualOrderPayments();
		$totalOrder = $order->getOrderTotals();
		$earning = $this->container->getParameter('collector_commission');
		
		$orderDetails = $this->renderView('InodataFloraBundle:Collection:_payments_details.html.twig',
				array('lastPayments'=>$lastPayments, 
						'actualPayments'=>$actualPayments,
						'totalOrder'=>$totalOrder, 
						'earning'=>$earning
				));
		
		return new Response(json_encode(array("details"=>$orderDetails)));
	}
	
	public function payAllAction()
	{
		$collector = $this->getSelectedCollector();
		$em = $this->getDoctrine()->getManager();
		
		$orders = $em->getRepository('InodataFloraBundle:Order')
			->findBy(array('collector'=>$collector, 'status'=>'partiallypayment'));
		
		if($orders){
			foreach ($orders as $order){
				$this->payOrder($order);
			}
		}
		
		return new Response(json_encode(array('success'=>true)));
	}
	
	public function payOrderAction($orderId)
	{
		$order = $this->getDoctrine()
			->getRepository("InodataFloraBundle:Order")
			->find($orderId);
		
		if ($order){
			$this->payOrder($order);
		}
		
		return new Response(json_encode(array('success'=>true)));
	}
	
	public function payOrder($order)
	{
		$em = $this->getDoctrine()->getManager();
		$order->setStatus('closed');
		$em->persist($order);
		$em->flush();
	}
	
	public function reasignOrderAction()
	{
		$collectorId = $this->get('request')->get('collectorId');
		$orderId = $this->get('request')->get('orderId');
		 
		$em = $this->getDoctrine()->getManager();
		$order = $em->getRepository("InodataFloraBundle:Order")
			->find($orderId);
		$collector = $em->getRepository("InodataFloraBundle:Employee")
			->find($collectorId);
		 
		$order->setCollector($collector);
		$em->persist($order);
		$em->flush();
		 
		return new Response(json_encode(array("success"=>true)));
	}
	
	private function setFilters($request)
	{
		if (!$request && $this->getRequest()->get('filters')){
			$this->setDateStart(null);
			$this->setDateEnd(null);
			$this->setSelectedStatus(null);
		}else
		{
			if(isset($request['collector']['value']))	{
				$this->setSelectedCollector($request['collector']['value']);
			}
			if (isset($request['deliveryDate']['value']['start'])){
				$this->setDateStart($request['deliveryDate']['value']['start']);
			}
			if (isset($request['deliveryDate']['value']['end'])){
				$this->setDateEnd($request['deliveryDate']['value']['end']);
			}
			if (isset($request['status']['value'])){
				$this->setSelectedStatus($request['status']['value']);
			}
		}
	}
	
	protected function setSelectedCollector($idCollector)
	{
		if ($idCollector!=0){
			$this->getRequest()->getSession()->set('collector_selected', $idCollector);
		}
	}
	
	protected function getSelectedCollector($idDefault=null)
	{
		$idCollector = $this->getRequest()->getSession()->get('collector_selected');
		if (!$idCollector){
			$this->setSelectedCollector($idDefault);
			return $idDefault;
		}
		 
		return $idCollector;
	}
	
	protected function setListView($view)
	{
		$this->getRequest()->getSession()->set('list_view', $view);
	}
	
	protected function getListeView()
	{
		$listView = $this->getRequest()->getSession()->get('list_view');
		if (!$listView) {
			$this->setListView('quick');
			return 'quick';
		}
		return $listView;
	}
	
	/**
	 * Sobreescr la function que permite usar el controller con ajax
	 */
	public function configure()
	{
		$adminCode = $this->container->get('request')->get('_sonata_admin');
	
		if($adminCode){
			parent::configure();
		}
	}
	
	private function setDateStart($date){
		$this->getRequest()->getSession()->set('collection_date_start', $date);
	}
	
	protected function getDateStart()
	{
		$date = $this->getRequest()->getSession()->get('collection_date_start');
		if (!$date){ //By default return TODAY
			$date = date("Y-m-01");
		}
		 
		return $date." 00:00:00";
	}
	
	private function setDateEnd($date){
		$this->getRequest()->getSession()->set('collection_date_end', $date);
	}
	
	protected function getDateEnd()
	{
		$date = $this->getRequest()->getSession()->get('collection_date_end');
		if (!$date){ //By default return TODAY
			$date = date("Y-m-d");
		}
			
		return $date." 23:59:59";
	}
	
	private function setSelectedStatus($status){
		$this->getRequest()->getSession()->set('collection_status', $status);
	}
	
	private function getSelectedStatus(){
		return $this->getRequest()->getSession()->get('collection_status');
	}
}
