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
        
        $product = new Product();
        $product->setCode('00005');
        $product->setDescription('6 Rosas Rojas');
        $product->setPrice('800');
        $product->setStock('8');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto5', $product);
        
        $product = new Product();
        $product->setCode('00006');
        $product->setDescription('Rosas rosas cristal');
        $product->setPrice('300');
        $product->setStock('10');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto6', $product);
        
        $product = new Product();
        $product->setCode('00007');
        $product->setDescription('Jarron surtido grande');
        $product->setPrice('500');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto7', $product);
        
        $product = new Product();
        $product->setCode('00008');
        $product->setDescription('Cofre Botanero');
        $product->setPrice('350');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto8', $product);
        
        $product = new Product();
        $product->setCode('00009');
        $product->setDescription('Canasta de madera frutal');
        $product->setPrice('250');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto9', $product);
        
        $product = new Product();
        $product->setCode('00010');
        $product->setDescription('Cubeta de Cerveza');
        $product->setPrice('280');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto10', $product);
        
        $product = new Product();
        $product->setCode('00011');
        $product->setDescription('Corona 70 cm');
        $product->setPrice('380');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto11', $product);
        
        $product = new Product();
        $product->setCode('00012');
        $product->setDescription('Corona 80 cm');
        $product->setPrice('400');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto12', $product);
        
        $product = new Product();
        $product->setCode('00013');
        $product->setDescription('Cubeta de Cerveza');
        $product->setPrice('280');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto13', $product);
        
        $product = new Product();
        $product->setCode('00014');
        $product->setDescription('Corona de 3 ruedas');
        $product->setPrice('500');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto14', $product);
        
        $product = new Product();
        $product->setCode('00015');
        $product->setDescription('Corona americana');
        $product->setPrice('280');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto15', $product);
             
        
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }
}