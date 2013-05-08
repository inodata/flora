<?php
namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\OrderProduct;

class LoadOrderProductData extends AbstractFixture implements OrderedFixtureInterface
{
	public function load(ObjectManager $manager)
	{
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order1'));
		$orderProduct->setProduct($this->getReference('producto1'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct1', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order2'));
		$orderProduct->setProduct($this->getReference('producto5'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct2', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order3'));
		$orderProduct->setProduct($this->getReference('producto19'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct3', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order4'));
		$orderProduct->setProduct($this->getReference('producto12'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct4', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order5'));
		$orderProduct->setProduct($this->getReference('producto6'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct5', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order6'));
		$orderProduct->setProduct($this->getReference('producto20'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct6', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order7'));
		$orderProduct->setProduct($this->getReference('producto12'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct7', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order8'));
		$orderProduct->setProduct($this->getReference('producto19'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct8', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order9'));
		$orderProduct->setProduct($this->getReference('producto17'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct9', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order10'));
		$orderProduct->setProduct($this->getReference('producto21'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct10', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order11'));
		$orderProduct->setProduct($this->getReference('producto20'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct11', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order12'));
		$orderProduct->setProduct($this->getReference('producto15'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct12', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order13'));
		$orderProduct->setProduct($this->getReference('producto5'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct13', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order14'));
		$orderProduct->setProduct($this->getReference('producto6'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct14', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order15'));
		$orderProduct->setProduct($this->getReference('producto9'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct15', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order16'));
		$orderProduct->setProduct($this->getReference('producto9'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct16', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order17'));
		$orderProduct->setProduct($this->getReference('producto20'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct17', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order18'));
		$orderProduct->setProduct($this->getReference('producto1'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct18', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order19'));
		$orderProduct->setProduct($this->getReference('producto11'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct19', $orderProduct);
		
		$orderProduct =new OrderProduct();
		$orderProduct->setOrder($this->getReference('order20'));
		$orderProduct->setProduct($this->getReference('producto6'));
		$orderProduct->setQuantity('1');
		$orderProduct->setProductPrice('');
		$manager->persist($orderProduct);
		$manager->flush();
		$this->addReference('orderProduct20', $orderProduct);
	}
	
	public function getOrder()
	{
		return 13;
	}
}