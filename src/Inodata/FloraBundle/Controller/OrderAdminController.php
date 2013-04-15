<?php

namespace Inodata\FloraBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Inodata\FloraBundle\Entity\Product;
use Inodata\FloraBundle\Entity\Order;
use Inodata\FloraBundle\Entity\OrderProduct;
use Inodata\FloraBundle\Entity\PaymentContact;
use Sonata\AdminBundle\Controller\CRUDController as Controller;

class OrderAdminController extends Controller
{	
	/**
	 * @param mixed $id
	 *
	 * @return Response
	 */
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
	
	public function orderProductsAction($id = null)
	{
		$price_subtotal = 0;
		
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
				
				$price_subtotal += $product->getPrice()*$cant;
				
				$listFields.= $this->renderView('InodataFloraBundle:Order:_product_item.html.twig', 
						array('product' => $product, 'total'=>$cant));
			}
		}
		
		$selectOptions = $this->renderView('InodataFloraBundle:Order:_select_order_option.html.twig', array('products' => $order));
		
		$response = array("listFields"=>$listFields, "selectOptions"=>$selectOptions, 
						  'totals'=>$this->getTotalsCost($id, $price_subtotal));
		
		return new Response(json_encode($response));
	}
	/**
	 * Calculate total price for the order
	 * @return array
	 */
	protected function getTotalsCost($orderId, $priceSubtotal)
	{
		$order = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Order')
			->find($orderId);
		
		$priceShipping = $order->getShipping();
		if(!$priceShipping){
			$priceShipping = 0;
		}
		$priceDiscount = $order->getDiscount();
		if (!$priceDiscount){
			$priceDiscount = 0;
		}
		$priceIVA = ($priceSubtotal+$priceShipping-$priceDiscount)*0.16;
		$priceTotal = $priceSubtotal+$priceShipping-$priceDiscount+$priceIVA;
		
		return array('shipping'=>$priceShipping, 'discount' =>$priceDiscount,
					 'iva' =>$priceIVA, 'subtotal'=>$priceSubtotal, 'total'=>$priceTotal);
	}
	
	public function paymentContactAction($id)
	{
		$paymentContact = $this->getDoctrine()
			->getRepository('InodataFloraBundle:PaymentContact')
			->find($id);
		
		$response = $this->getPaymentCotactInfoAsArray($paymentContact);
		
		return new Response(json_encode($response));
	}
	
	private function getPaymentCotactInfoAsArray($paymentContact)
	{
		return array(
				'id' => $paymentContact->getId(),
				'name' => $paymentContact->getName(),
				'emp_number' => $paymentContact->getEmployeeNumber(),
				'phone' => $paymentContact->getPhone(),
				'email' => $paymentContact->getEmail(),
				'department' => $paymentContact->getDepartment()
		);
	}
	
	public function editAction($id = null)
	{
		if ($this->getRestMethod() == 'POST'){
			$this->preUpdate($id);
			$this->updatePaymentContactInfo();
		}
		
		return parent::editAction($id);
	}
	
	protected function preUpdate($id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$orderProducts = $em->getRepository('InodataFloraBundle:OrderProduct')
			->findByOrderId($id);
		
		if ($orderProducts)
		{
			foreach ($orderProducts as $product){
				$em->remove($product);
			}
			
			$em->flush();
			$em->clear();
		}
	}
	
	public function createAction()
	{
		if ($this->getRestMethod() == 'POST'){
			$this->updatePaymentContactInfo();
		}
		
		return parent::createAction();
	}
	
	private function updatePaymentContactInfo()
	{
		$orderArray = $this->get('request')->get($this->get('request')->get('uniqid'));
		$paymentContactId = $orderArray['paymentContact'];
			
		$em = $this->getDoctrine()->getEntityManager();
		$paymentContact = $em->getRepository('InodataFloraBundle:PaymentContact')
			->find($paymentContactId);
		
		if ($orderArray['customer'])
		{
			$customer = $em->getRepository('InodataFloraBundle:Customer')
			->find($orderArray['customer']);
			
			$paymentContact->setCustomer($customer);
		}
		
		$paymentContact->setName($orderArray['Contacto']['name']);
		$paymentContact->setEmployeeNumber($orderArray['Contacto']['employeeNumber']);
		$paymentContact->setPhone($orderArray['Contacto']['phone']);
		$paymentContact->setEmail($orderArray['Contacto']['email']);
		$paymentContact->setDepartment($orderArray['Contacto']['department']);
			
		$em->persist($paymentContact);
		$em->flush();
		$em->clear();
	}
	
	public function createPaymentContactAction()
	{
		$name = $this->get('request')->get('contactName');
		
		$em = $this->getDoctrine()->getEntityManager();
		$paymentContact = new PaymentContact();
		$paymentContact->setName($name);
		
		$em->persist($paymentContact);
		$em->flush();
		$response = $this->getPaymentCotactInfoAsArray($paymentContact);
		
		return New Response(json_encode($response));
	}
	
	public function filterPaymentContactsByCustomerAction($customerId)
	{
		$paymentContacts = $this->getdoctrine()
			->getRepository('InodataFloraBundle:PaymentContact')
			->findByCustomer($customerId);
		
		$response = array('contacts' => $this->renderView('InodataFloraBundle:Order:_dinamic_select_item.html.twig', 
				array('contacts' => $paymentContacts)));
		
		return new Response(json_encode($response));
	}
	
	public function filterMessagesByCategoryAction($categoryId)
	{
		$query = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Message')
			->createQueryBuilder('m');
		
		if ($categoryId!=0){
			$query->where('m.category=:category')
				->setParameter('category', $categoryId);
		}
		
		$messages = $query->getquery()->getResult();
			
		$response = array('messages'=>$this->renderView('InodataFloraBundle:Order:_dinamic_select_item.html.twig',
				array('messages'=>$messages)));
		
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