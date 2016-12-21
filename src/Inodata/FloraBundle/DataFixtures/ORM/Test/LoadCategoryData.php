<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setDescription('Para Mamá Mayo');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category1', $category);

        $category = new Category();
        $category->setDescription('	Arreglos de Amor y Amistad 2013');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category2', $category);

        $category = new Category();
        $category->setDescription('Arreglos Día de la Secretaria');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category3', $category);

        $category = new Category();
        $category->setDescription('Arreglos de Rosas');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category4', $category);

        $category = new Category();
        $category->setDescription('Arreglos para Caballeros');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category5', $category);

        $category = new Category();
        $category->setDescription('Arreglos de Eventos');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category6', $category);

        $category = new Category();
        $category->setDescription('Arreglos Surtidos');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category7', $category);

        $category = new Category();
        $category->setDescription('	Arreglos Funerales');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category8', $category);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
