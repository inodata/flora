<?php

namespace Inodata\FloraBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Inodata\FloraBundle\Entity\Product;

class OrderController extends Controller
{	
	public function productAction($id)
	{
		$product = $this->getDoctrine()
			->getRepository('InodataFloraBundle:Product')
			->find($id);
		
		$content = $this->renderView('InodataFloraBundle:Order:_product_item.html.twig', array('product' => $product));
		
		return new Response($content);
	}
}