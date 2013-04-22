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
    
    /* TODO:ERMOVE*/
    public function ordersByMessengerIdAction()
    {

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
    
    public function addOrdersToMessengerAction(Request $request)
    {
    	$id = $request->get('orderIds');
    	$response = array('messengerId' => $request->get('messengerId'),
    			'firstOrderId' => $id[0]	);
    	
    	//$response = array( 'request' => $this->container->get('request')->get('_sonata_admin'));
    	
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