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
        $customer->setPaymentAddress($this->getReference('direccion1'));

        $manager->persist($customer);
        $manager->flush();

        $this->addReference('patito_sa', $customer);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
    	return 3;
    }
}