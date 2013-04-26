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
        }*/
        
        $render = $this->render($this->admin->getTemplate('list'), array(
        		'action'   => 'list',
        		'form'     => $formView,
        		'distribution_form' => $distributionFormView,
        		'datagrid' => $datagrid
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
    
    public function addOrdersToMessengerAction($messengerId, $orderIds)
    {
    	
    	$orderIds = explode("+", $orderIds);
    	unset( $orderIds[ count($orderIds)-1 ]);
    	
    	$messenger = $this->getDoctrine()
    	    			  ->getRepository('InodataFloraBundle:Employee')
    					  ->find( $messengerId );
    	
    	
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
    	//$this->addFlash('sonata_flash_success', 'Se asignaron las ordenes al repartidor '.$messenger->getName().' '.$messenger->getLastName());
    	
    	$emptyList = $this->renderView('InodataFloraBundle:Distribution:_distribution_assign_empty_list.html.twig', array());
    	
    	$response = array('messenger' => $orderIds, 'empty_list' => $emptyList);
    	return new Response(json_encode($response));    
    }
    
    public function verifyOrderStatusAction($orderId)
    {
    	$order = $this->getDoctrine()
    				  ->getRepository('InodataFloraBundle:Order')
    				  ->find( $orderId );
    	$option = $this->renderView('InodataFloraBundle:Distribution:_order_option.html.twig',
    			array('id' => $order->getId()));
    	$emptyList = $this->renderView('InodataFloraBundle:Distribution:_distribution_assign_empty_list.html.twig', array());
    	
    	$response = array( 'isValidToAdd' => ( $order->getStatus() == 'open' && $order->getMessenger() == null) ? 'true' : 'false', 
    			           'option' => $option ,
    					   'empty_list' => $emptyList
    					);
    	return new Response(json_encode($response));    	
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
    	$render = $this->renderView('InodataFloraBundle:Distribution:print_distribution.html.twig', array(
    				'base_template' => null,
    				
    			));
    	return new Response($render);
    }
    
    public function deliveredAction()
    {
    	return $this->setOrderStatus('delivered');
    }
    
    public function closedAction()
    {
    	return $this->setOrderStatus('closed');
    }
    
    public function openAction()
    {
		return $this->setOrderStatus('open');
    }
    
    private function setOrderStatus($status)
    {
    	$id = $this->get('request')->get('id');
    	
    	if( isset($id))
    	{
    		$order =  $this->getDoctrine()
    			->getRepository('InodataFloraBundle:Order')
    			->find( $id );
    	
    		if( $order == null ){
    			//TODO: Flash Object Not Found
    		} else{
    			$order->setStatus($status);
    			$em = $this->getDoctrine()->getEntityManager();
    			$em->persist($order);
    			$em->flush();
    		}
    	}
    	
    	return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));    	
    }
    
}