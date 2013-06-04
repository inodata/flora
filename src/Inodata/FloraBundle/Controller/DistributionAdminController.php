<?php

namespace Inodata\FloraBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Inodata\FloraBundle\Entity\Product;
use Inodata\FloraBundle\Entity\Order;
use Inodata\FloraBundle\Form\Type\DistributionType;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class DistributionAdminController extends Controller
{
    public function listAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }
        
        $this->setFilters($this->get('request')->get('filter'));

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();
        
        $order = new Order();
        $distributionFormView = $this->createForm(new DistributionType(), $order)->createView();
		
        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());
        
        $messengers = $this->getDoctrine()
        				->getRepository('InodataFloraBundle:Employee')
        				->findByJobPosition('Messenger');
        $firstTab = 0;
        $lastTab = 0;
        
        if($messengers){
        	$firstTab = $messengers[0]->getId();
        	$lastTab = $messengers[count($messengers)-1]->getId();
        }
        

        isset($messengers[0])==true? $firsMessenger = $messengers[0]->getId():$firsMessenger=0;
        
        $render = $this->render($this->admin->getTemplate('list'), array(
        		'action'   => 'list',
        		'form'     => $formView,
        		'distribution_form' => $distributionFormView,
        		'datagrid' => $datagrid,
        		'messengers' => $messengers,
        		'first_tab' => $firstTab,
        		'last_tab' => $lastTab,
        		'selected_messenger' => $this->getSelectedMessenger($firsMessenger)
        ));
        
        return $render;
    }
    
    private function setFilters($request)
    {
    	if (!$request && $this->getRequest()->get('filters')){
    		$this->setDateSelected('');
    	}else
    	{
    		if ($request['deliveryDate']['value']){
    			$this->setDateSelected($request['deliveryDate']['value']);
    		}
    		if ($request['messenger']['value']){
    			$this->setSelectedMessenger($request['messenger']['value']);
    		}
    	}
    }
    
    public function addOrdersToMessengerAction()
    {
    	/* Obtiene los valores por post */
    	$messengerId = $this->get('request')->get('messenger_id');
    	$orderIds = $this->get('request')->get('order_ids');
    	
    	/* Encuentra el Messenger */
    	$messenger = $this->getDoctrine()
    	    			  ->getRepository('InodataFloraBundle:Employee')
    					  ->find( $messengerId );
    	if( !$messenger ){
    		return new Response( json_encode( array ( 'status' => 'messenger_not_found') ) );
    	}
    	
    	foreach( $orderIds as $orderId)
    	{
    		$em = $this->getDoctrine()->getEntityManager();
    		$order = $this->getDoctrine()
    					  ->getRepository('InodataFloraBundle:Order')
    		   			  ->find( $orderId );
    		
    		if( $order != null )
    		{
	    		$order->setStatus('intransit');
	    		$order->setMessenger($messenger);	 
	    		
	    		$em->persist($order);
	    		$em->flush();
    		}
    	}
    	
    	$messengerFullName = $messenger->getName().' '.$messenger->getLastName();
    	$transMessage = $this->get('translator')->trans('alert.distribution_assigned_success', array( 'messenger' => $messengerFullName), 'InodataFloraBundle');
    	$this->addFlash('sonata_flash_success', $transMessage);
    	
    	$emptyList = $this->renderView('InodataFloraBundle:Distribution:_distribution_assign_empty_list.html.twig', array());
    	
    	$response = array('status' => 'success' );
    	return new Response(json_encode($response));
    }
    
    public function updateOrdersAvailableAction()
    {
    	//Pedidos preasignados a repartidor
    	$ordersPreAsigned = array_filter($this->get('request')->get('orders'));
    	
    	$orders = $this->getDoctrine()->getRepository('InodataFloraBundle:Order')
    		->createQueryBuilder('o')
    		->where("o.status = 'open'")
    		->andWhere('o.messenger IS NULL')
    		->getQuery()->getResult();
    	
    	$orderOptions =  $this->renderView('InodataFloraBundle:Distribution:_order_option.html.twig',
    			array('orders' => $orders, 'preAsigned'=>$ordersPreAsigned));
    	
    	return new Response(json_encode(array('orderOptions'=>$orderOptions)));
    }
    
    public function configure()
    {
    	$adminCode = $this->container->get('request')->get('_sonata_admin');
    
    	if($adminCode){
    		parent::configure();
    	}
    }
    
    public function printDistributionAction()
    {
    	 $messengers = $this->getDoctrine()
    	 	  				->getRepository('InodataFloraBundle:Employee')
    	 	  				->findByJobPosition('messenger');
    	 
    	 $filterMessenger = array();
    	 
    	 foreach ( $messengers as $messenger )
    	 {
    	 	$orders = $this->getDoctrine()
    	 	  				->getRepository('InodataFloraBundle:Order')
    	 	  				->findBy( array('status' => 'intransit',
    	 	  								'messenger' => $messenger->getId()
    	 	  						));
    	 	if( $orders ){
    	 		$messenger->setOrders( $orders );
    	 		array_push($filterMessenger, $messenger);
    	 	}
    	 }
    	 
    	 
    	 $render = $this->render('InodataFloraBundle:Distribution:print_distribution.html.twig', array(
        	'base_template' => 'SonataAdminBundle:CRUD:base_list.html.twig',
    	 	'messengers' => $filterMessenger,
    	 	'date' => new \DateTime('NOW')
        ));
        
        return $render;
    }
    
    public function changeOrderStatusAction()
    {
    	$id = $this->get('request')->get('orderId');
    	$status = $this->get('request')->get('action');
    	
    	if($status =="deliver-all"){
    		$orders = $this->getDoctrine()->getRepository('InodataFloraBundle:Order')
    			->findBy(array('messenger'=>$this->getSelectedMessenger(), 'status'=>'intransit'));
    		
    		foreach ($orders as $order){
    			$this->setOrderStatus('delivered', $order->getId());
    		}
    	}else{
    		$this->setOrderStatus($status, $id);
    	}
    	
    	$orderOptions = null;
    	if ($status == 'open'){
    		$ordersOpened = $this->getDoctrine()
    		->getRepository('InodataFloraBundle:Order')
    		->findByStatus('open');
    		
    		$orderOptions = $this->renderView('InodataFloraBundle:Distribution:_order_option.html.twig',
    				array('orders' => $ordersOpened));
    	}
    	
    	return new Response(json_encode(array('success'=>$status, 'orderOptions'=>$orderOptions)));
    }
    
    private function setOrderStatus($status, $id)
    {
    	if( isset($id))
    	{
    		$order =  $this->getDoctrine()
    			->getRepository('InodataFloraBundle:Order')
    			->find($id);
    	
    		if( $order == null ){
    			// TODO: No se cambia el status
    		} else{
    			$order->setStatus($status);
    			if ($status=='open'){
    				$order->setMessenger(null);
    			}
    			$em = $this->getDoctrine()->getEntityManager();
    			$em->persist($order);
    			$em->flush();
    		}
    	}
    }
    
    public function batchActionDeliveredAll()
    {
    	$orderIds = $this->get('request')->get('idx', array());
    		
    	foreach( $orderIds as $orderId )
    	{
    		$this->setOrderStatus('delivered', $orderId);
    	} 
    	
    	return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }
    
    public function addOrderToMessengerAction()
    {
    	/* Obtiene los valores por post */
    	$messengerId = $this->get('request')->get('messenger_id');
    	$orderId = $this->get('request')->get('order_id');
    	 
    	/* Encuentra el Messenger */
    	$messenger = $this->getDoctrine()->getRepository('InodataFloraBundle:Employee')
    		->find( $messengerId );
    	 
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$order = $this->getDoctrine()->getRepository('InodataFloraBundle:Order')
    		->find( $orderId );
    	 
    	if( $order != null ){
    		$order->setStatus('intransit');
    		$order->setMessenger($messenger);
    		 
    		$em->persist($order);
    		$em->flush();
    	}
    	
    	$row = $this->renderView('InodataFloraBundle:Distribution:_list_item.html.twig', 
    			array('orders' => array(0=>$order)));
    	
    	//Cargar View con estos datos
    	$ordersOpened = $this->getDoctrine()
    		->getRepository('InodataFloraBundle:Order')
    		->findByStatus('open');
    	
    	$orderOptions = $this->renderView('InodataFloraBundle:Distribution:_order_option.html.twig', 
    			array('orders' => $ordersOpened));
    	
    	$nInTransit = $this->getNOrdersInStatus('intransit', $messengerId);
    	$nDelivered = $this->getNOrdersInStatus('delivered', $messengerId);
    	 
    	return new Response(json_encode(array('order'=>$row, 
    			'id'=>$messengerId, 'orderOptions'=>$orderOptions,
    			'n_delivered'=>$nDelivered, 'n_in_transit'=>$nInTransit)));
    }
    
    /**MODIFICADO EN LA SEGUNDA VERSION**/
    
    public function loadOrdersByMessengerAction($id)
    {
    	$this->setSelectedMessenger($id);
    	$id = $this->getSelectedMessenger();
    	
    	$orders = $this->getDoctrine()
    		->getRepository('InodataFloraBundle:Order')
    		->createQueryBuilder('o')
    		->where("o.messenger=:id AND (o.status='intransit' OR o.status='delivered')")
    		->andWhere('o.deliveryDate=:date')
    		->orderBy('o.status', 'ASC')
    		->setParameters(array('id'=>$id, 'date'=>$this->getDateSelected()))
    		->getQuery()->getResult();
    	
    	$messenger = $this->getDoctrine()
    		->getRepository('InodataFloraBundle:Employee')
    		->find($id);
    	
    	//$numOrders = $this->getNOrdersInTransit($id);
    	$nOrdersInTransit = $this->getNOrdersInStatus('intransit', $id);
    	$nOrdersDelivered = $this->getNOrdersInStatus('delivered', $id);
    	
    	$response = $this->renderView('InodataFloraBundle:Distribution:_list_item.html.twig', 
    			array('orders' => $orders));
    	
    	return new Response(json_encode(array('orders'=>$response, 
    			'id'=>$id, 'n_in_transit'=>$nOrdersInTransit, 'n_delivered' => $nOrdersDelivered,
    			'boxes'=>$messenger->getBoxes(), 'lamps'=>$messenger->getLamps())));
    }
    
    /**
     * @TODO validar filtro por fechapara ambos  casos
     */
    private function getNOrdersInStatus($status, $messengerId)
    {
    	$nOrders = $this->getDoctrine()
    		->getRepository('InodataFloraBundle:Order')
    		->createQueryBuilder('o')
    		->select('COUNT(o.id)')
    		->where("o.messenger=:id AND o.status=:status")
    		->andWhere("o.deliveryDate=:date")
    		->setParameters(array('id'=>$messengerId, 'status'=>$status, 
    				'date'=>$this->getDateSelected()))
    		->getQuery()->getSingleScalarResult();
    	
    	return $nOrders;
    }
    
    //Messenger edit in place
    public function editInPlaceAction()
    {
    	$idColumn = explode('-', $this->get('request')->get('id'));
    
    	$employeeId = $idColumn[0];
    	$employeeAttr = $idColumn[1];
    	$value =  $this->get('request')->get('value');
    
    	$em = $this->getDoctrine()->getEntityManager();
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
    
    	return new Response($value);
    }
    
    //Modify objects total (lamps, boxes)
    public function editObjectsAction()
    {
    	$object = $this->get('request')->get('object');
    	$action = $this->get('request')->get('action');
    	$messengerId = $this->getSelectedMessenger();
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$messenger = $em->getRepository('InodataFloraBundle:Employee')
    		->find($messengerId);
    	
    	if($object=='boxes'){
    		$nBoxes = $messenger->getBoxes();
    		if (!$nBoxes){
    			$nBoxes = 0;
    		}
    		if ($action == '-' && $nBoxes > 0){
    			$nBoxes--;
    		}
    		if($action == '+'){
    			$nBoxes++;
    		}
    		
    		$messenger->setBoxes($nBoxes);
    	}
    	
    	if ($object == 'lamps'){
    		$nLamps = $messenger->getLamps();
    		if(!$nLamps){
    			$nLamps = 0;
    		}
    		if ($action=='-' && $nLamps > 0){
    			$nLamps--;
    		}
    		if($action=='+'){
    			$nLamps++;
    		}
    		$messenger->setLamps($nLamps);
    	}
    	
    	$em->persist($messenger);
    	$em->flush();
    	
    	$object =='boxes'? $newValue=$messenger->getBoxes():$newValue=$messenger->getLamps();
    	
    	return new Response(json_encode(array('object'=>$object, 'value'=>$newValue)));
    }
    
    protected function setSelectedMessenger($idMessenger)
    {
    	if ($idMessenger!=0){
    		$this->getRequest()->getSession()->set('messenger_selected', $idMessenger);
    	}
    }
    
    protected function getSelectedMessenger($idDefault=null)
    {
    	$idMessenger = $this->getRequest()->getSession()->get('messenger_selected');
    	if (!$idMessenger){
    		$this->setSelectedMessenger($idDefault);
    		return $idDefault;
    	}
    	
    	return $idMessenger;
    }
    
    protected function setDateSelected($date)
    {
    	$this->getRequest()->getSession()->set('delivery_date', $date);
    }
    
    protected function getDateSelected()
    {
    	$date = $this->getRequest()->getSession()->get('delivery_date');
    	if (!$date){ //By default return TODAY
    		return date("Y-m-d");
    	}
    	
    	return $date;
    }
}
