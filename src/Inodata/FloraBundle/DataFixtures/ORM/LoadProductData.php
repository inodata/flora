<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Product;

class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setCode('00001');
        $product->setDescription('Paquete 10 rosas');
        $product->setPrice('650');
        $product->setStock('10');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto1', $product);
        
        $product = new Product();
        $product->setCode('00002');
        $product->setDescription('Cisne con 2 rosas');
        $product->setPrice('740');
        $product->setStock('10');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto2', $product);
        
        $product = new Product();
        $product->setCode('00003');
        $product->setDescription('Arreglo de flores');
        $product->setPrice('520');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto3', $product);
        
        $product = new Product();
        $product->setCode('00004');
        $product->setDescription('Arreglo de flores con mensaje');
        $product->setPrice('720');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto4', $product);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }
}