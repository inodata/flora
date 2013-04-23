<?php

namespace Inodata\FloraBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        return $this->render($this->admin->getTemplate('list'), array(
            'action'   => 'list',
            'form'     => $formView,
        	'distribution_form' => $distributionFormView,
            'datagrid' => $datagrid
        ));
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
	    		
	    		$em->flush();
	    		$em->persist($order);
    		}
    	}
    	
    	
    	$response = array('messenger' => $orderIds );
    	return new Response(json_encode($response));    
    }
    
    public function verifyOrderStatusAction($orderId)
    {
    	$order = $this->getDoctrine()
    				  ->getRepository('InodataFloraBundle:Order')
    				  ->find( $orderId );
    	$option = $this->renderView('InodataFloraBundle:Distribution:_order_option.html.twig',
    			array('id' => $order->getId()));
    	
    	$response = array( 'isValidToAdd' => ( $order->getStatus() == 'open' && $order->getMessenger() == null) ? 'true' : 'false', 
    			           'option' => $option );
    	return new Response(json_encode($response));    	
    }
    
    public function configure()
    {
    	$adminCode = $this->container->get('request')->get('_sonata_admin');
    
    	if($adminCode){
    		parent::configure();
    	}
    }
    
}