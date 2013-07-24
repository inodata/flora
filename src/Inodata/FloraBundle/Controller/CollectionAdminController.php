<?php
namespace Inodata\FloraBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Inodata\FloraBundle\Entity\Product;
use Inodata\FloraBundle\Entity\Order;
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
	
		//$this->setFilters($this->get('request')->get('filter'));
	
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
				'collectors' => $collectors,
				'first_tab' => $firstTab,
				'last_tab' => $lastTab,
				'selected_collector' => $this->getSelectedCollector($firstCollector),
				'list_view' => $this->getListeView(),
				'orders'	=> $this->getCollectorOrders($this->getSelectedCollector($firstCollector))
		));
	
		return $render;
	}
	
	private function getCollectorOrders($collectorId)
	{
		$orders = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Order')
			->findByCollector($collectorId);
		
		return $orders;
	}
	
	
	public function loadOrdersByCollectorAction($id)
	{
		$this->setSelectedCollector($id);
		$id = $this->getSelectedCollector();
		//$status = $this->getStatusSelected();
		//TODO: implementar carga de orders de acuerdo al filtro
		
		$orders = $this->getCollectorOrders($id);
		 
		$response = $this->renderView('InodataFloraBundle:Collection:_list_item.html.twig',
				array('orders' => $orders));
		 
		return new Response(json_encode(array('orders'=>$response,
				'id'=>$id /*'n_in_transit'=>$nOrdersInTransit, 'n_delivered' => $nOrdersDelivered,
				'boxes'=>$messenger->getBoxes(), 'lamps'=>$messenger->getLamps()*/)));
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
			 
			$em->persist($order);
			$em->flush();
		}
		 
		$row = $this->renderView('InodataFloraBundle:Collection:_list_item.html.twig',
				array('orders' => array(0=>$order)));
		 
		//Cargar View con estos datos
		$ordersDelivered = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Order')
			->findByStatus('delivered');
		 
		$orderOptions = $this->renderView('InodataFloraBundle:Distribution:_order_option.html.twig',
				array('orders' => $ordersDelivered));
		
		//TODO: Cargar informacion de las ganancias delcobrador
		//$nInTransit = $this->getNOrdersInStatus('intransit', $messengerId);
		//$nDelivered = $this->getNOrdersInStatus('delivered', $messengerId);
	
		return new Response(json_encode(array('order'=>$row,
				'id'=>$collectorId, 'orderOptions'=>$orderOptions
				/*'n_delivered'=>$nDelivered, 'n_in_transit'=>$nInTransit*/)));
	}
	
	private function setFilters($request)
	{
		if (!$request && $this->getRequest()->get('filters')){
			$this->setDateSelected('');
			$this->setStatusSelected(null);
		}else
		{
			if (isset($request['deliveryDate']['value'])){
				$this->setDateSelected($request['deliveryDate']['value']);
			}
			if (isset($request['collector']['value'])){
				$this->setSelectedCollector($request['collector']['value']);
			}
			if (isset($request['status']['value'])){
				$this->setStatusSelected($request['status']['value']);
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
}
