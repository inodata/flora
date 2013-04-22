<?php
namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Partner;

class LoadPartnerData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
    	  $partner =new Partner();
    	  $partner->setCode('4589');
    	  $partner->setName('Miguel Ayala');
    	  $partner->setPhone('8181359684');
    	  $partner->setEmail('miguel.ayala@gmail.com');
    	  $partner->setAddress($this->getReference('direccion13'));
    	  $manager->persist($partner);
    	  $manager->flush();
    	  $this->addReference('partner1',$partner);
    	
    	  $partner =new Partner();
    	  $partner->setCode('4589');
    	  $partner->setName('Miguel Ayala');
    	  $partner->setPhone('8181359684');
    	  $partner->setEmail('miguel.ayala@gmail.com');
    	  $partner->setAddress($this->getReference('direccion14'));
    	  $manager->persist($partner);
    	  $manager->flush();
    	  $this->addReference('partner2',$partner);
    	
    	  $partner =new Partner();
    	  $partner->setCode('4589');
    	  $partner->setName('Miguel Ayala');
    	  $partner->setPhone('8181359684');
    	  $partner->setEmail('miguel.ayala@gmail.com');
    	  $partner->setAddress($this->getReference('direccion15'));
    	  $manager->persist($partner);
    	  $manager->flush();
    	  $this->addReference('partner3',$partner);
    	
    	  $partner =new Partner();
    	  $partner->setCode('4589');
    	  $partner->setName('Miguel Ayala');
    	  $partner->setPhone('8181359684');
    	  $partner->setEmail('miguel.ayala@gmail.com');
    	  $partner->setAddress($this->getReference('direccion16'));
    	  $manager->persist($partner);
    	  $manager->flush();
    	  $this->addReference('partner4',$partner);
    	
    	  $partner =new Partner();
    	  $partner->setCode('4589');
    	  $partner->setName('Miguel Ayala');
    	  $partner->setPhone('8181359684');
    	  $partner->setEmail('miguel.ayala@gmail.com');
    	  $partner->setAddress($this->getReference('direccion17'));
    	  $manager->persist($partner);
    	  $manager->flush();
    	  $this->addReference('partner5',$partner);
    	
    	  $partner =new Partner();
    	  $partner->setCode('4589');
    	  $partner->setName('Miguel Ayala');
    	  $partner->setPhone('8181359684');
    	  $partner->setEmail('miguel.ayala@gmail.com');
    	  $partner->setAddress($this->getReference('direccion18'));
    	  $manager->persist($partner);
    	  $manager->flush();
    	  $this->addReference('partner6',$partner);
    	
    	  $partner =new Partner();
    	  $partner->setCode('4589');
    	  $partner->setName('Miguel Ayala');
    	  $partner->setPhone('8181359684');
    	  $partner->setEmail('miguel.ayala@gmail.com');
    	  $partner->setAddress($this->getReference('direccion19'));
    	  $manager->persist($partner);
    	  $manager->flush();
    	  $this->addReference('partner7',$partner);
    	
    	  $partner =new Partner();
    	  $partner->setCode('4589');
    	  $partner->setName('Miguel Ayala');
    	  $partner->setPhone('8181359684');
    	  $partner->setEmail('miguel.ayala@gmail.com');
    	  $partner->setAddress($this->getReference('direccion20'));
    	  $manager->persist($partner);
    	  $manager->flush();
    	  $this->addReference('partner8',$partner);
    	
    
    }
/**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 9;
    }
}