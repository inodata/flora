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
    	  $order->addProduct($this->getReference('producto1'));
    	  $order->addProduct($this->getReference('producto2'));
    	  $order->addProduct($this->getReference('producto3'));
    	  $order->addProduct($this->getReference('producto3'));
    	  $order->setShippingAddress($this->getReference('direccion21'));
    	  $order->setCustomer($this->getReference('customer1'));
    	  $order->setPaymentContact($this->getReference('paymentcontact10'));
    	  $order->setStatus('open');
    	  $manager->persist($order);
    	  $manager->flush();
    	  $this->addReference('order1',$order);
    	
    	
    	  $order =new Order();
    	  $order->setDeliveryDate(new \DateTime('2013-03-02 11:25:50'));
    	  $order->setInvoiceNumber('A0002');
    	  $order->setShipping('70');
    	  $order->setDiscount('30');
    	  $order->setCreator($this->getReference('user2'));
    	  $order->setCreatedAt(new \DateTime('2013-03-02 11:25:50'));
    	  $order->setFrom('Pedro');
    	  $order->setTo('Karla');
    	  $order->setMessage($this->getReference('message2'));
    	  $order->addProduct($this->getReference('producto4'));
    	  $order->addProduct($this->getReference('producto5'));
    	  $order->setShippingAddress($this->getReference('direccion22'));
    	  $order->setCustomer($this->getReference('customer2'));
    	  $order->setPaymentContact($this->getReference('paymentcontact2'));
    	  $order->setStatus('open');
    	  $manager->persist($order);
    	  $manager->flush();
    	  $this->addReference('order2',$order);
    	
    	  $order =new Order();
    	  $order->setDeliveryDate(new \DateTime('2013-03-02 12:25:50'));
    	  $order->setInvoiceNumber('A0003');
    	  $order->setShipping('75');
    	  $order->setDiscount('40');
    	  $order->setCreator($this->getReference('user3'));
    	  $order->setCreatedAt(new \DateTime('2013-03-02 12:25:50'));
    	  $order->setFrom('Pablo');
    	  $order->setTo('Laura');
    	  $order->setMessage($this->getReference('message4'));
    	  $order->addProduct($this->getReference('producto6'));
    	  $order->addProduct($this->getReference('producto7'));
    	  $order->setShippingAddress($this->getReference('direccion23'));
    	  $order->setCustomer($this->getReference('customer6'));
    	  $order->setPaymentContact($this->getReference('paymentcontact6'));
    	  $order->setStatus('open');
    	  $manager->persist($order);
    	  $manager->flush();
    	  $this->addReference('order3',$order);
    }
    
/**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 12;
    }
    
}