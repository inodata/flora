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
		
		$listField = $this->renderView('InodataFloraBundle:Order:_product_item.html.twig', 
				array('product' => $product, 'total' => 1));
		$selectOption = $this->renderView('InodataFloraBundle:Order:_select_order_option.html.twig', 
				array('id' => $product->getId()));
		$response = array('listField' => $listField, 'selectOption' => $selectOption, 'id' => 'product-'.$id);
		
		return new Response(json_encode($response));
	}
	
	/**
	 * 
	 * @param int $id
	 * @return json
	 */
	public function orderProductsAction($id = null)
	{
		if($id){
			$order = $this->getDoctrine()
				->getRepository('InodataFloraBundle:OrderProduct')
				->findByOrderId($id);
			
			$productIds = array();
			foreach ($order as $product){
				if(isset($productIds[$product->getProductId()])){
					$productIds[$product->getProductId()]+=1;
				}else{
					$productIds[$product->getProductId()]=1;
				}
			}
			
			$listFields="";
			foreach ($productIds as $productId=>$cant){
				$product = $this->getDoctrine()
				->getRepository('InodataFloraBundle:Product')
				->find($productId);
				
				$listFields.= $this->renderView('InodataFloraBundle:Order:_product_item.html.twig', 
						array('product' => $product, 'total'=>$cant));
			}
		}
		
		$selectOptions = $this->renderView('InodataFloraBundle:Order:_select_order_option.html.twig', array('products' => $order));
		$response = array("listFields"=>$listFields, "selectOptions"=>$selectOptions);
		
		return new Response(json_encode($response));
	}
}