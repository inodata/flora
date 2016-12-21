<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Product;

class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setCode('451');
        $product->setDescription('Espectacular jarron con 36 Rosas');
        $product->setPrice('1595');
        $product->setStock('10');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto1', $product);

        $product = new Product();
        $product->setCode('452');
        $product->setDescription('Hermoso diseño con imagen de  Virgen de Guadalupe');
        $product->setPrice('795');
        $product->setStock('10');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto2', $product);

        $product = new Product();
        $product->setCode('453');
        $product->setDescription('Arreglo de 12 rosas rojas con malla envolvente');
        $product->setPrice('595');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto3', $product);

        $product = new Product();
        $product->setCode('454');
        $product->setDescription('Hermoso topiario con Gerberas y Rosas');
        $product->setPrice('495');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto4', $product);

        $product = new Product();
        $product->setCode('455');
        $product->setDescription('Hermosa canasta surtida  arreglada');
        $product->setPrice('1895');
        $product->setStock('8');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto5', $product);

        $product = new Product();
        $product->setCode('456');
        $product->setDescription('24 Rosas en topario canasta ');
        $product->setPrice('1195');
        $product->setStock('10');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto6', $product);

        $product = new Product();
        $product->setCode('457');
        $product->setDescription('Hemoso arreglo de Tulipanes');
        $product->setPrice('595');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto7', $product);

        $product = new Product();
        $product->setCode('458');
        $product->setDescription('Arreglo de 6 Rosas rojas en base de madera');
        $product->setPrice('395');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto8', $product);

        $product = new Product();
        $product->setCode('459');
        $product->setDescription('Arreglo de Rosas y Mariposas');
        $product->setPrice('595');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto9', $product);

        $product = new Product();
        $product->setCode('460');
        $product->setDescription('Arreglo de Flores surtidas');
        $product->setPrice('495');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto10', $product);

        $product = new Product();
        $product->setCode('461');
        $product->setDescription('Arreglo de 3 Rosas rojas en base de madera ');
        $product->setPrice('295');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto11', $product);

        $product = new Product();
        $product->setCode('462');
        $product->setDescription('Elegante canasta de Frutas y Agapandos');
        $product->setPrice('695');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto12', $product);

        $product = new Product();
        $product->setCode('463');
        $product->setDescription('Hermosa Jaulita Primaveral');
        $product->setPrice('595');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto13', $product);

        $product = new Product();
        $product->setCode('464');
        $product->setDescription('Caja de madera musical con 12 Rosas rojas');
        $product->setPrice('795');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto14', $product);

        $product = new Product();
        $product->setCode('465');
        $product->setDescription('Arreglo tropical Heliconias en base de madera');
        $product->setPrice('495');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto15', $product);

        $product = new Product();
        $product->setCode('466');
        $product->setDescription('Canasta de lilis y peluche');
        $product->setPrice('795');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto16', $product);

        $product = new Product();
        $product->setCode('467');
        $product->setDescription('Hermosa canasta surtida ');
        $product->setPrice('595');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto17', $product);

        $product = new Product();
        $product->setCode('468');
        $product->setDescription('Espectacular canasta 36 Rosas vegetativo');
        $product->setPrice('1595');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto18', $product);

        $product = new Product();
        $product->setCode('469');
        $product->setDescription('Hermoso diseño de Girasoles y Rosas');
        $product->setPrice('595');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto19', $product);

        $product = new Product();
        $product->setCode('470');
        $product->setDescription('Arreglo de Tulipanes en Alhajero');
        $product->setPrice('695');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto20', $product);

        $product = new Product();
        $product->setCode('471');
        $product->setDescription('Arreglo Elegancia');
        $product->setPrice('1295');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto21', $product);

        $product = new Product();
        $product->setCode('472');
        $product->setDescription('Cofre mimbre con 24 Rosas');
        $product->setPrice('1195');
        $product->setStock('9');
        $manager->persist($product);
        $manager->flush();
        $this->addReference('producto22', $product);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 4;
    }
}
