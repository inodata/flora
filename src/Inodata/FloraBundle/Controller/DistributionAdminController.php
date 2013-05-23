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

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();
        
        $order = new Order();
        $distributionFormView = $this->createForm(new DistributionType(), $order)->createView();
		
        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());
		
        /*
        if( $this->getRequest()->isXmlHttpRequest() ){
        	$render = $this->renderView('SonataAdminBundle:CRUD:list.html.twig', array(
        			'action'   => 'list',
        			'base_template' => $this->getBaseTemplate(),
        			'admin' => $this->admin,
        			'form'     => $formView,
        			'distribution_form' => $distributionFormView,
        			'datagrid' => $datagrid));
        	
        	return new Response($render);
        }
        */
        
        $messengers = $this->getDoctrine()
        				->getRepository('InodataFloraBundle:Employee')
        				->findByJobPosition('Messenger');
        $firstTab = 0;
        $lastTab = 0;
        
        if($messengers){
        	$firstTab = $messengers[0]->getId();
        	$lastTab = $messengers[count($messengers)-1]->getId();
        }
         
        
        print_r($this->getSelectedMessenger($messengers[0]->getId())); //exit();
        
        $render = $this->render($this->admin->getTemplate('list'), array(
        		'action'   => 'list',
        		'form'     => $formView,
        		'distribution_form' => $distributionFormView,
        		'datagrid' => $datagrid,
        		'messengers' => $messengers,
        		'first_tab' => $firstTab,
        		'last_tab' => $lastTab,
        		'selected_messenger' => $this->getSelectedMessenger($messengers[0]->getId())
        ));
        
        return $render;
    }
    
    public function addPreviewOrderToMessengerAction($orderId)
    {
    	$order = $this->getDoctrine()
				    	->getRepository('InodataFloraBundle:Order')
				    	->find( $orderId );
    	
    	$row = $this->renderView('InodataFloraBundle:Distribution:_order_item.html.twig',
    			array('order' => $order));
    
    	$response = array('row' => $row);
    	
    	return new Response(json_encode($response));
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
    		->where("o.status = 'open'")->andWhere('o.messenger IS NULL')
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
    
    public function deliveredAction()
    {
    	$id = $this->get('request')->get('id');
    	$this->setOrderStatus('delivered', $id);
    	return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }
    
    public function closedAction()
    {
    	$id = $this->get('request')->get('id');
    	$this->setOrderStatus('closed', $id);
    	return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }
    
    public function openAction()
    {
    	$id = $this->get('request')->get('id');
		$this->setOrderStatus('open', $id);
		return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }
    
    private function setOrderStatus($status, $id)
    {
    	if( isset($id))
    	{
    		$order =  $this->getDoctrine()
    			->getRepository('InodataFloraBundle:Order')
    			->find( $id );
    	
    		if( $order == null ){
    			// TODO: No se cambia el status
    		} else{
    			$order->setStatus($status);
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
    
    /**MODIFICADO EN LA SEGUNDA VERSION**/
    
    public function loadOrdersByMessengerAction($id)
    {
    	$this->setSelectedMessenger($id);
    	
    	$orders = $this->getDoctrine()
    		->getRepository('InodataFloraBundle:Order')
    		->createQueryBuilder('o')
    		->where("o.messenger=:id AND (o.status='intransit' OR o.status='delivered')")
    		->orderBy('o.status', 'ASC')
    		->setParameter('id', $id)
    		->getQuery()->getResult();
    	
    	$response = $this->renderView('InodataFloraBundle:Distribution:_list_item.html.twig', array('orders' => $orders));
    	
    	return new Response(json_encode(array('orders'=>$response, 'id'=>$id)));
    }
    
    protected function setSelectedMessenger($idMessenger)
    {
    	$this->getRequest()->getSession()->set('messenger_selected', $idMessenger);
    }
    
    protected function getSelectedMessenger($idDefault=null)
    {
    	$idMessenger = $this->getRequest()->getSession()->get('messenger_selected');
    	/*if (!$idMessenger){
    		$this->setSelectedMessenger($idDefault);
    		return $idDefault;
    	}*/
    	
    	return $idMessenger;
    }
}
