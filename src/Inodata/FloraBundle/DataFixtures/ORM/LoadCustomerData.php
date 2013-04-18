<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Customer;

class LoadCustomerData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $customer = new Customer();
        $customer->setRfc('ABC1010102a9');
        $customer->setBusinessName('Patito SA de CV');
        $customer->setCompanyName('Industrias Patito Incorporated');
        $customer->setDiscount(.05);
        $customer->setFiscalAddress($this->getReference('direccion1'));
        $customer->setPaymentAddress($this->getReference('direccion2'));

        $manager->persist($customer);
        $manager->flush();

        $this->addReference('customer1', $customer);
        
        $customer = new Customer();
        $customer->setRfc('MAH128345GH2');
        $customer->setBusinessName('Super Colchones SA de CV');
        $customer->setCompanyName('Super Colchones');
        $customer->setDiscount(.07);
        $customer->setFiscalAddress($this->getReference('direccion3'));
        $customer->setPaymentAddress($this->getReference('direccion4'));
        
        $manager->persist($customer);
        $manager->flush();
        
        $this->addReference('customer2', $customer);
        
        $customer = new Customer();
        $customer->setRfc('MAH128345GH2');
        $customer->setBusinessName('Industrias Gonzalez');
        $customer->setCompanyName('Super Colchones');
        $customer->setDiscount(.05);
        $customer->setFiscalAddress($this->getReference('direccion5'));
        $customer->setPaymentAddress($this->getReference('direccion6'));
        
        $manager->persist($customer);
        $manager->flush();
        
        $this->addReference('customer3', $customer);
        
        $customer = new Customer();
        $customer->setRfc('MAH128345GH2');
        $customer->setBusinessName('Industrias Perez');
        $customer->setCompanyName('LOS Perez');
        $customer->setDiscount(.1);
        $customer->setFiscalAddress($this->getReference('direccion7'));
        $customer->setPaymentAddress($this->getReference('direccion8'));
        
        $manager->persist($customer);
        $manager->flush();
        
        $this->addReference('customer4', $customer);
        
        $customer = new Customer();
        $customer->setRfc('MAH128345GH2');
        $customer->setBusinessName('Industrias Gomez');
        $customer->setCompanyName('Gomez Company');
        $customer->setDiscount(0);
        $customer->setFiscalAddress($this->getReference('direccion9'));
        $customer->setPaymentAddress($this->getReference('direccion10'));
        
        $manager->persist($customer);
        $manager->flush();
        
        $this->addReference('customer5', $customer);
        
        $customer = new Customer();
        $customer->setRfc('MAH128345GH2');
        $customer->setBusinessName('industrias texitles Rocha');
        $customer->setCompanyName('Los Rochas');
        $customer->setDiscount(.04);
        $customer->setFiscalAddress($this->getReference('direccion11'));
        $customer->setPaymentAddress($this->getReference('direccion12'));
        
        $manager->persist($customer);
        $manager->flush();
        
        $this->addReference('customer6', $customer);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
    	return 7;
    }
}