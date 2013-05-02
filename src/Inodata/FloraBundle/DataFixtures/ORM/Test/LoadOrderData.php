<?php
namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Order;

class LoadOrderData extends AbstractFixture implements OrderedFixtureInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
    	  $order =new Order();
    	  $order->setDeliveryDate(new \DateTime('2013-03-02 10:25:50'));
    	  $order->setInvoiceNumber('A0001');
     	  $order->setShipping('65');
    	  $order->setDiscount('50');
    	  $order->setCreator($this->getReference('user1'));
    	  $order->setCreatedAt(new \DateTime('2013-03-02 10:25:50'));
    	  $order->setFrom('Maria');
    	  $order->setTo('Juan');
    	  $order->setMessage($this->getReference('message1'));
    	  $order->setShippingAddress($this->getReference('direccion21'));
    	  $order->setCustomer($this->getReference('customer1'));
    	  $order->setPaymentContact($this->getReference('paymentcontact10'));
    	  $order->setStatus('open');
    	  $manager->persist($order);
    	  $manager->flush();
    	  $this->addReference('order1',$order);
    	  
    	  $order =new Order();
    	  $order->setDeliveryDate(new \DateTime('2013-03-02 12:40:50'));
    	  $order->setInvoiceNumber('A0004');
    	  $order->setShipping('75');
    	  $order->setDiscount('40');
    	  $order->setCreator($this->getReference('user1'));
    	  $order->setCreatedAt(new \DateTime('2013-03-02 12:25:50'));
    	  $order->setFrom('Juan');
    	  $order->setTo('Maria');
    	  $order->setMessage($this->getReference('message5'));
    	  $order->setShippingAddress($this->getReference('direccion24'));
    	  $order->setCustomer($this->getReference('customer7'));
    	  $order->setPaymentContact($this->getReference('paymentcontact7'));
    	  $order->setStatus('open');
    	  $manager->persist($order);
    	  $manager->flush();
    	  $this->addReference('order2',$order);
    	  
    	  for($i=3; $i<=100; $i++)
    	  {
	    	  $order =new Order();
	    	  $order->setDeliveryDate(new \DateTime('2013-03-02 12:40:50'));
	    	  $order->setInvoiceNumber('A0004');
	    	  $order->setShipping('75');
	    	  $order->setDiscount('40');
	    	  $order->setCreator($this->getReference('user1'));
	    	  $order->setCreatedAt(new \DateTime('2013-03-02 12:25:50'));
	    	  $order->setFrom('Juan');
	    	  $order->setTo('Maria');
	    	  $order->setMessage($this->getReference('message5'));
	    	  $order->setShippingAddress($this->getReference('direccion24'));
	    	  $order->setCustomer($this->getReference('customer7'));
	    	  $order->setPaymentContact($this->getReference('paymentcontact7'));
	    	  $order->setStatus('open');
	    	  $manager->persist($order);
	    	  $manager->flush();
	    	  $this->addReference('order'.$i,$order);
    	  }
    }
    
/**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 12;
    }
    
}