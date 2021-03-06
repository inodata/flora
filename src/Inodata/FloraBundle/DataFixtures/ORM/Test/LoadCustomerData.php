<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Customer;

class LoadCustomerData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
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
        $customer->setCompanyName('Los Perez');
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
        $customer->setBusinessName('Industrias texitles Gutierrez');
        $customer->setCompanyName('Los Gutierrez');
        $customer->setDiscount(.04);
        $customer->setFiscalAddress($this->getReference('direccion11'));
        $customer->setPaymentAddress($this->getReference('direccion12'));

        $manager->persist($customer);
        $manager->flush();

        $this->addReference('customer6', $customer);

        $customer = new Customer();
        $customer->setRfc('MAH128345GH2');
        $customer->setBusinessName('Industrias Bimbo');
        $customer->setCompanyName('Bimbo');
        $customer->setDiscount(.04);
        $customer->setFiscalAddress($this->getReference('direccion13'));
        $customer->setPaymentAddress($this->getReference('direccion14'));

        $manager->persist($customer);
        $manager->flush();

        $this->addReference('customer7', $customer);

        $customer = new Customer();
        $customer->setRfc('MAH128345GH2');
        $customer->setBusinessName('Duques Asociados');
        $customer->setCompanyName('Los Duques');
        $customer->setDiscount(.04);
        $customer->setFiscalAddress($this->getReference('direccion15'));
        $customer->setPaymentAddress($this->getReference('direccion16'));

        $manager->persist($customer);
        $manager->flush();

        $this->addReference('customer8', $customer);

        $customer = new Customer();
        $customer->setRfc('MAH128345GH2');
        $customer->setBusinessName('Industrias Almeida');
        $customer->setCompanyName('Patito Jr');
        $customer->setDiscount(.04);
        $customer->setFiscalAddress($this->getReference('direccion17'));
        $customer->setPaymentAddress($this->getReference('direccion18'));

        $manager->persist($customer);
        $manager->flush();

        $this->addReference('customer9', $customer);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 7;
    }
}
