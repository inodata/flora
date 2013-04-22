<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\PaymentContact;

class LoadPaymentContactData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
     * {@inheritDoc}
     */
	
  public function load(ObjectManager $manager)
    {
			  $paymentcontact =new PaymentContact();
			  $paymentcontact->setEmployeenumber('12345');
			  $paymentcontact->setName('Jose Perez');
			  $paymentcontact->setPhone('8111845854');
			  $paymentcontact->setEmail('jose.perez@gmail.com');
			  $paymentcontact->setDepartment('Finanzas');
			  $paymentcontact->setCustomer($this->getReference('customer2'));
			  $manager->persist($paymentcontact);
			  $manager->flush();
			  $this->addReference('paymentcontact1',$paymentcontact);
			
			  $paymentcontact =new PaymentContact();
			  $paymentcontact->setEmployeenumber('23456');
			  $paymentcontact->setName('Jorge Gonzalez');
			  $paymentcontact->setPhone('811184000');
			  $paymentcontact->setEmail('jorge.gonzalez@gmail.com');
			  $paymentcontact->setDepartment('RH');
			  $paymentcontact->setCustomer($this->getReference('customer2'));
			  $manager->persist($paymentcontact);
			  $manager->flush();
			  $this->addReference('paymentcontact2',$paymentcontact);
			
			
			  $paymentcontact =new PaymentContact();
			  $paymentcontact->setEmployeenumber('4611564');
			  $paymentcontact->setName('Ana Gutierrez');
			  $paymentcontact->setPhone('8111896581');
			  $paymentcontact->setEmail('ana.gutierrez@gmail.com');
			  $paymentcontact->setDepartment('RH');
			  $paymentcontact->setCustomer($this->getReference('customer4'));
			  $manager->persist($paymentcontact);
			  $manager->flush();
			  $this->addReference('paymentcontact3',$paymentcontact);
			
			  $paymentcontact =new PaymentContact();
			  $paymentcontact->setEmployeenumber('267889');
			  $paymentcontact->setName('Carlos Hermosillo');
			  $paymentcontact->setPhone('8181234567');
			  $paymentcontact->setEmail('carlos.hermosillo@gmail.com');
			  $paymentcontact->setDepartment('Deportes');
			  $paymentcontact->setCustomer($this->getReference('customer4'));
			  $manager->persist($paymentcontact);
			  $manager->flush();
			  $this->addReference('paymentcontact4',$paymentcontact);
			
			  $paymentcontact =new PaymentContact();
			  $paymentcontact->setEmployeenumber('41255255');
			  $paymentcontact->setName('Jorge Campos');
			  $paymentcontact->setPhone('8181475289');
			  $paymentcontact->setEmail('jorge.campos@gmail.com');
			  $paymentcontact->setDepartment('Deportes');
			  $paymentcontact->setCustomer($this->getReference('customer5'));
			  $manager->persist($paymentcontact);
			  $manager->flush();
			  $this->addReference('paymentcontact5',$paymentcontact);
			
			  $paymentcontact =new PaymentContact();
			  $paymentcontact->setEmployeenumber('3698255');
			  $paymentcontact->setName('Roberto Carlos');
			  $paymentcontact->setPhone('8111845854');
			  $paymentcontact->setEmail('jose.perez@gmail.com');
			  $paymentcontact->setDepartment('Finanzas');
			  $paymentcontact->setCustomer($this->getReference('customer6'));
			  $manager->persist($paymentcontact);
			  $manager->flush();
			  $this->addReference('paymentcontact6',$paymentcontact);
			
			  $paymentcontact =new PaymentContact();
			  $paymentcontact->setEmployeenumber('2125861');
			  $paymentcontact->setName('Maria Dominguez');
			  $paymentcontact->setPhone('8112358974');
			  $paymentcontact->setEmail('maria.dominguez@gmail.com');
			  $paymentcontact->setDepartment('Ventas');
			  $paymentcontact->setCustomer($this->getReference('customer6'));
			  $manager->persist($paymentcontact);
			  $manager->flush();
			  $this->addReference('paymentcontact7',$paymentcontact);
			
			  $paymentcontact =new PaymentContact();
			  $paymentcontact->setEmployeenumber('3514935');
			  $paymentcontact->setName('Jorge Ramirez');
			  $paymentcontact->setPhone('811457845');
			  $paymentcontact->setEmail('jorge.ramirez@gmail.com');
			  $paymentcontact->setDepartment('Sistemas');
			  $paymentcontact->setCustomer($this->getReference('customer6'));
			  $manager->persist($paymentcontact);
			  $manager->flush();
			  $this->addReference('paymentcontact8',$paymentcontact);
			
			  $paymentcontact =new PaymentContact();
			  $paymentcontact->setEmployeenumber('2478965');
			  $paymentcontact->setName('Gustavo Mendez');
			  $paymentcontact->setPhone('8111382830');
			  $paymentcontact->setEmail('gustavo.mendez@gmail.com');
			  $paymentcontact->setDepartment('Direccion');
			  $paymentcontact->setCustomer($this->getReference('customer1'));
			  $manager->persist($paymentcontact);
			  $manager->flush();
			  $this->addReference('paymentcontact9',$paymentcontact);
			
			  $paymentcontact =new PaymentContact();
			  $paymentcontact->setEmployeenumber('1284567');
			  $paymentcontact->setName('Francisco Rocha');
			  $paymentcontact->setPhone('8111899689');
			  $paymentcontact->setEmail('francisco.rocha@gmail.com');
			  $paymentcontact->setDepartment('Direccion');
			  $paymentcontact->setCustomer($this->getReference('customer1'));
			  $manager->persist($paymentcontact);
			  $manager->flush();
			  $this->addReference('paymentcontact10',$paymentcontact);
			  	
    }

/**
     * {@inheritDoc}
     */
    public function getOrder()
    {
    	return 10;
    }
}