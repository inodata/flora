<?php

namespace Inodata\FloraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Inodata\FloraBundle\Entity\Product;
use Inodata\FloraBundle\Entity\Order;
use Inodata\FloraBundle\Entity\OrderProduct;
//use Sonata\AdminBundle\Controller\CRUDController as Controller;

class OrderAdminController extends Controller
{	
	public function productAction($id)
	{
		$product = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Product')
			->find($id);
		
		$content = $this->renderView('InodataFloraBundle:Order:_product_item.html.twig', array('products' => array($product)));
		
		return new Response($content);
	}
	
	/**
	 * 
	 * @param int $id
	 * @return json
	 */
	public function orderProductsAction($id = null)
	{
		$products = array();
		
		if($id){
			$order = $this->getDoctrine()
				->getRepository('InodataFloraBundle:OrderProduct')
				->findByOrderId($id);
			
			foreach ($order as $product){
				$products[] = $this->getDoctrine()
					->getRepository('InodataFloraBundle:Product')
					->find($product->getProductId());
			}
		}
		
		$listFields = $this->renderView('InodataFloraBundle:Order:_product_item.html.twig', array('products' => $products));
		$selectOptions = null;
		
		$response = array("listFields"=>$listFields, "selecOptions"=>$selectOptions);
		
		return new Response(json_encode($response));
	}
}