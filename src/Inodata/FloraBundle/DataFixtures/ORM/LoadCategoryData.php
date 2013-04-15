<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setDescription('Amistad');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category1', $category);
        
        $category = new Category();
        $category->setDescription('14 de Febrero');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category2', $category);
        
        $category = new Category();
        $category->setDescription('Dia de las madres');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category3', $category);
        
        $category = new Category();
        $category->setDescription('Amistad y cumpleaños');
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category4', $category);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5;
    }
}