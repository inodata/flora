<?php

namespace Inodata\FloraBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Inodata\FloraBundle\Entity\Product;
use Inodata\FloraBundle\Entity\Order;
use Inodata\FloraBundle\Entity\Customer;
use Inodata\FloraBundle\Entity\Address;
use Inodata\FloraBundle\Entity\OrderProduct;
use Inodata\FloraBundle\Entity\PaymentContact;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Inodata\FloraBundle\Lib\NumberToLetter;

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
				array('product' => $product, 'total' => 1));
		
		$response = array('listField' => $listField, 'optionsToSave' => $selectOption, 'id' => $id);
		return new Response(json_encode($response));
	}
	
	public function orderProductsAction($id = null, $isForInvoice = false)
	{
		$price_subtotal = 0;
		
		$order = $this->getDoctrine()
			->getRepository('InodataFloraBundle:OrderProduct')
			->findByOrder($id);
		
		$listFields="";
		$selectOptions ="";
		foreach ($order as $orderProduct){
			$orderProduct->getProduct()->setPrice($orderProduct->getProductPrice());
			$listFields.= $this->renderView('InodataFloraBundle:Order:_product_item.html.twig',
					array('product' => $orderProduct->getProduct(), 
						  'total' =>$orderProduct->getQuantity()));	
			
			$selectOptions .= $this->renderView('InodataFloraBundle:Order:_select_order_option.html.twig',
					array('product' => $orderProduct->getProduct(),
						  'total' =>$orderProduct->getQuantity()));
		
			$price_subtotal+=($orderProduct->getProduct()->getPrice()*$orderProduct->getQuantity());
		}
		
		$response = array("listFields"=>$listFields, "optionsToSave"=>$selectOptions, 
						  'totals'=>$this->getTotalsCostAsArray($id, $price_subtotal));
		
		return new Response(json_encode($response));
	}
	
	public function addingProductAction()
	{
		$code =  $this->get('request')->get('code');
		$description = $this->get('request')->get('description');
		$price = $this->get('request')->get('price');
		
		if (!$code){
			$code ="0";
		}
		
		$em = $this->getDoctrine()->getManager();
		$product = new Product();
		$product->setCode($code);
		$product->setDescription($description);
		$product->setPrice($price);
		$product->setStock("1");
		
		$em->persist($product);
		$em->flush($product);
		
		$listField = $this->renderView('InodataFloraBundle:Order:_product_item.html.twig',
				array('product' => $product, 'total' => 1));
		$selectOption = $this->renderView('InodataFloraBundle:Order:_select_order_option.html.twig',
				array('product' => $product, 'total' => 1));
		
		$response = array('listField' => $listField, 'optionsToSave' => $selectOption, 'id' => $product->getId());
		return new Response(json_encode($response));
	}
	
	/**
	 * Calculate total price for the order
	 * @return array
	 */
	protected function getTotalsCostAsArray($orderId, $subtotal, $shipping=null, $discount=null, $hasInvoice=null)
	{
		$IVA = 0 ;
		
		if ($orderId != null)
		{
			$order = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Order')
			->find($orderId);
			
			$shipping = $order->getShipping();
			if(!$shipping){
				$shipping = 0;
			}
			$discount = $order->getDiscount();
			if (!$discount){
				$discount = 0;
			}
			
			$hasInvoice = $order->getHasInvoice();
		}
		
		$discountPercentLabel = '';
		$discountNet = $discount;
		
		if($discount<1){
			$discountNet = $subtotal*$discount;
			$discountPercentLabel = ($discount*100).'%';
		}
		
		$subtotal = $subtotal+$shipping-$discountNet;
		
		if ($hasInvoice){
			$IVA = $subtotal*0.16;
		}
		
		$total = $subtotal+$IVA;
		
		//Convierte el total a letras
		$numberLetter = new NumberToLetter();
		$numberLetter->setNumero($total);
		$totalInLetters = $numberLetter->getletras();
		
		setlocale(LC_MONETARY, 'es_MX');
		
		return array('shipping'=>$shipping, 
			'discount' =>$discount, 
			'discount_net' =>round($discountNet,2),
			'discount_percent'=>$discountPercentLabel, 
			'iva' =>money_format('%i', $IVA), 
			'subtotal'=>money_format('%i', $subtotal), 
			'total'=>money_format('%i', $total),
			'totalInLetters'=>$totalInLetters);
	}
	
	public function updateTotalsCostAction()
	{
		$shipping = $this->get('request')->get('shipping');
		$discount = $this->get('request')->get('discount');
		$products = $this->get('request')->get('products');
		$hasInvoice = $this->get('request')->get('hasInvoice');
		
		$subtotal=0;
		
		if ($products){
			$repository = $this->getDoctrine()->getRepository('InodataFloraBundle:Product');
			
			foreach ($products as $aProduct){
				$product = $repository->find($aProduct['id']);
				$subtotal += $product->getPrice()*$aProduct['amount'];
			}
		}
		
		$response = $this->getTotalsCostAsArray(null, $subtotal, $shipping, $discount, $hasInvoice);
		
		return new Response(json_encode(array('prices' =>$response)));
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
				'extension' => $paymentContact->getExtension(),
				'email' => $paymentContact->getEmail(),
				'department' => $paymentContact->getDepartment()
		);
	}
	
	public function editAction($id = null)
	{
		$products = $this->get('request')->get('product');
		
		$action = $this->getRequest()->getSession()->get('post_save_action');
		$this->getRequest()->getSession()->set('post_save_action', '');
		
		if ($this->getRequest()->getSession()->get('submit_action') == 'submit'){
			$this->getRequest()->getSession()->set('post_save_action', $action);
			$this->getRequest()->getSession()->set('submit_action', '');
		}
		
		if ($this->getRestMethod() == 'POST'){
			$this->preUpdate($id, $products);
			$this->updatePaymentContactInfo();
			//Try to create the invoice if not exist
			$this->createInvoice($id);
			
			$this->getRequest()->getSession()->set('submit_action', 'submit');
		}
		
		return parent::editAction($id);
	}
	
	protected function preUpdate($id, $newProducts=null)
	{
		$em = $this->getDoctrine()->getManager();
		$orderProducts = $em->getRepository('InodataFloraBundle:OrderProduct')
			->findByOrder($id);
		//Delete all order's products
		if ($orderProducts){
			foreach ($orderProducts as $product){
				$em->remove($product);
			}
			
			$em->flush();
			$em->clear();
		}
		
		$this->createOrderProducts($id, $newProducts);
		
	}
	
	/**
	 * CONSTRUCTING
	 * @param unknown_type $id
	 */
	private function createInvoice($id)
	{
		$em = $this->getDoctrine()->getManager();
		$order = $em->getRepository('InodataFloraBundle:Invoice')
			->findByOrder($id);	
	}
	/** --------------------------------*/
	
	public function createOrderProducts($orderId, $products=null)
	{
		$em = $this->getDoctrine()->getManager();
		if ($products){
			$order = $em->getRepository('InodataFloraBundle:Order')->find($orderId);
			foreach ($products as $productId => $quantity){
				$product = $em->getRepository('InodataFloraBundle:Product')->find($productId);
				$orderProduct = new OrderProduct();
				$orderProduct->setOrder($order);
				$orderProduct->setProduct($product);
				$orderProduct->setQuantity($quantity);
				$orderProduct->setProductPrice($product->getPrice());
				$em->persist($orderProduct);
			}
			$em->flush();
			$em->clear();
		}
	}
	
	public function createAction()
	{
		$uniqid = $this->get('request')->get('uniqid');
		$request = $this->get('request')->get($uniqid);
		
		$create = parent::createAction();
		
		if ($this->getRestMethod() == 'POST'){
			$this->updatePaymentContactInfo();
			
			$products = $this->get('request')->get('product');
			$object = $this->admin->getSubject();
			$this->createOrderProducts($object->getId(), $products);
			
			$this->getRequest()->getSession()->set('submit_action', 'submit');
		}
		
		return $create;
	}
	
	private function updatePaymentContactInfo()
	{
		$orderArray = $this->get('request')->get($this->get('request')->get('uniqid'));
		$paymentContactId = $orderArray['paymentContact'];
			
		$em = $this->getDoctrine()->getManager();
		$paymentContact = $em->getRepository('InodataFloraBundle:PaymentContact')
			->find($paymentContactId);
		
		if ($orderArray['customer'])
		{
			$customer = $em->getRepository('InodataFloraBundle:Customer')
			->find($orderArray['customer']);
			
			$paymentContact->setCustomer($customer);
		}
		
		$paymentContact->setName($orderArray['contact']['name']);
		$paymentContact->setEmployeeNumber($orderArray['contact']['employeeNumber']);
		$paymentContact->setPhone($orderArray['contact']['phone']);
		$paymentContact->setExtension($orderArray['contact']['extension']);
		$paymentContact->setEmail($orderArray['contact']['email']);
		$paymentContact->setDepartment($orderArray['contact']['department']);
			
		$em->persist($paymentContact);
		$em->flush();
		$em->clear();
	}
	
	public function createPaymentContactAction()
	{
		$name = $this->get('request')->get('contactName');
		
		$em = $this->getDoctrine()->getManager();
		$paymentContact = new PaymentContact();
		$paymentContact->setName($name);
		
		$em->persist($paymentContact);
		$em->flush();
		$response = $this->getPaymentCotactInfoAsArray($paymentContact);
		
		return New Response(json_encode($response));
	}
	
	public function filterPaymentContactsByCustomerAction($customerId)
	{
		$customerDiscount = 0;
		
		$query = $this->getDoctrine()
			->getRepository('InodataFloraBundle:PaymentContact')
			->createQueryBuilder('pc');
		
		if($customerId!=0){
			$query->where('pc.customer=:customer')
				->setParameter('customer', $customerId);
			
			$customer = $this->getDoctrine()
				->getRepository('InodataFloraBundle:Customer')
				->find($customerId);
			
			$customerDiscount = $customer->getDiscount();
		}
		
		$paymentContacts = $query->getQuery()->getResult();
		
		$response = array(
				'customer_discount' => $customerDiscount,
				'contacts' => $this->renderView('InodataFloraBundle:Order:_dynamic_select_item.html.twig', 
					array('contacts' => $paymentContacts)
				));
		
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
		
		$messages = $query->getQuery()->getResult();
			
		$response = array('messages'=>$this->renderView('InodataFloraBundle:Order:_dynamic_select_item.html.twig',
				array('messages'=>$messages)));
		
		return new Response(json_encode($response));
	}
	
	//INVOICE CUSTOMER EDIT IN PLACE
	public function editInPlaceAction()
	{
		$updateAddress= false;
		
		$idColumn = explode('-', $this->get('request')->get('id'));
		
		$customerId = $idColumn[0];
		$customerAttr = $idColumn[1]; 
		$value =  $this->get('request')->get('value');
		
		$em = $this->getDoctrine()->getManager();
		$customer = $em->getRepository('InodataFloraBundle:Customer')
			->find($customerId);
		
		if ($customerAttr!='bussinessName' && $customerAttr!='rfc'){
			$fiscalAddress = $customer->getFiscalAddress();
			$updateAddress=true;
		}
		
		switch($customerAttr){
			case 'bussinessName':
				$customer->setBusinessName($value);
			break;
			case 'rfc':
				$customer->setRfc($value);
			break;
			case 'street':
				$fiscalAddress->setStreet($value);
			break;
			case 'noExt':
				$fiscalAddress->setNoExt($value);
			break;
			case 'noExt':
				$fiscalAddress->setNoInt($value);
			break;
			case 'neighborhood':
				$fiscalAddress->setNeighborhood($value);
			break;
			case 'city':
				$fiscalAddress->setCity($value);
			break;
			case 'state':
				$fiscalAddress->setState($value);
			break;
			case 'zip':
				$fiscalAddress->setPostalCode($value);
			break;
		};
		
		if ($updateAddress){
			$em->persist($fiscalAddress);
		}else {
			$em->persist($customer);
		}
		
		$em->flush();
		$em->clear();
		
		return new Response($value);
	}
	
	//Overwitten function
	public function redirectTo($object)
	{
		$response = parent::redirectTo($object);
		
		if ($this->get('request')->get('save_and_print_note')){
			$this->getRequest()->getSession()->set('post_save_action', 'print-note');
		}
		if ($this->get('request')->get('save_and_print_invoice')){
			$this->getRequest()->getSession()->set('post_save_action', 'print-invoice');
		}
		if ($this->get('request')->get('btn_update_and_list') ||
			$this->get('request')->get('btn_create_and_list') ||
			$this->get('request')->get('btn_create_and_create'))
		{
			$this->getRequest()->getSession()->set('post_save_action', '');
		}
		
		return $response;
	}
	
	public function configure()
	{
		$adminCode = $this->container->get('request')->get('_sonata_admin');
		
		if($adminCode){
			parent::configure();
		}
	}
}
