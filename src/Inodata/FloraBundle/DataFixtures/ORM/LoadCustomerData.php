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

        $this->addReference('patito_sa', $customer);
        
        $customer = new Customer();
        $customer->setRfc('MAH128345GH2');
        $customer->setBusinessName('Super Colchones SA de CV');
        $customer->setCompanyName('Super Colchones');
        $customer->setDiscount(.05);
        $customer->setFiscalAddress($this->getReference('direccion3'));
        $customer->setPaymentAddress($this->getReference('direccion4'));
        
        $manager->persist($customer);
        $manager->flush();
        
        $this->addReference('customer2', $customer);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
    	return 3;
    }
}