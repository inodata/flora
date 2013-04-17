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
        
        $address = new Address();
        $address->setStreet('Lazaro Cardenas');
        $address->setNoInt('A');
        $address->setNoExt('1234');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion2', $address);
        
        $address = new Address();
        $address->setStreet('Av. Revolución');
        $address->setNoInt('A');
        $address->setNoExt('4562');
        $address->setNeighborhood('Col. Altavista');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion3', $address);
        
        $address = new Address();
        $address->setStreet('Av. Del Estado');
        $address->setNoInt('123');
        $address->setNoExt('4325');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion4', $address);
        
        $address = new Address();
        $address->setStreet('Lazaro Cardenas');
        $address->setNoInt('A');
        $address->setNoExt('1234');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion5', $address);
        
        $address = new Address();
        $address->setStreet('Lazaro Cardenas');
        $address->setNoInt('A');
        $address->setNoExt('1234');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion6', $address);
        
        $address = new Address();
        $address->setStreet('Calle pelluelas');
        $address->setNoInt('A');
        $address->setNoExt('2345');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion7', $address);
                
        $address = new Address();
        $address->setStreet('Luis Donaldo Colosio');
        $address->setNoInt('A');
        $address->setNoExt('901');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion8', $address);
        
        $address = new Address();
        $address->setStreet('Abraham Lincoln');
        $address->setNoInt('A');
        $address->setNoExt('825');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion9', $address);
        
        $address = new Address();
        $address->setStreet('Av Insurgentes');
        $address->setNoInt('A');
        $address->setNoExt('600');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion10', $address);
        
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
        $this->addReference('direccion11', $address);
        
        $address = new Address();
        $address->setStreet('Lazaro Cardenas');
        $address->setNoInt('A');
        $address->setNoExt('1234');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion12', $address);
        
        $address = new Address();
        $address->setStreet('Av. Revolución');
        $address->setNoInt('A');
        $address->setNoExt('4562');
        $address->setNeighborhood('Col. Altavista');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion13', $address);
        
        $address = new Address();
        $address->setStreet('Av. Del Estado');
        $address->setNoInt('123');
        $address->setNoExt('4325');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion14', $address);
        
        $address = new Address();
        $address->setStreet('Lazaro Cardenas');
        $address->setNoInt('A');
        $address->setNoExt('1234');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion15', $address);
        
        $address = new Address();
        $address->setStreet('Lazaro Cardenas');
        $address->setNoInt('A');
        $address->setNoExt('1234');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion16', $address);
        
        $address = new Address();
        $address->setStreet('Calle pelluelas');
        $address->setNoInt('A');
        $address->setNoExt('2345');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion17', $address);
                
        $address = new Address();
        $address->setStreet('Luis Donaldo Colosio');
        $address->setNoInt('A');
        $address->setNoExt('901');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion18', $address);
        
        $address = new Address();
        $address->setStreet('Abraham Lincoln');
        $address->setNoInt('A');
        $address->setNoExt('825');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion19', $address);
        
        $address = new Address();
        $address->setStreet('Av Insurgentes');
        $address->setNoInt('A');
        $address->setNoExt('600');
        $address->setNeighborhood('Centro');
        $address->setCity('Monterrey');
        $address->setState('NLE');
        $address->setPostalCode('64000');
        $manager->persist($address);
        $manager->flush();
        $this->addReference('direccion20', $address);
        
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}