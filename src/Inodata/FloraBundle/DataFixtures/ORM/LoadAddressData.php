<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Address;

class LoadAddressData extends AbstractFixture implements OrderedFixtureInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $address = new Address();
        $address->setStreet('Rio Tamesi');
        $address->setNoInt('A');
        $address->setNoExt('1234');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');        
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion1', $address);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}