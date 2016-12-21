<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Employee;

class LoadEmployeeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $employee = new Employee();
        $employee->setCode('12548');
        $employee->setName('Carlos');
        $employee->setLastName('Perez Gutierrez');
        $employee->setPhone('8182585859');
        $employee->setjobPosition('Messenger');
        $manager->persist($employee);
        $manager->flush();
        $this->addReference('employee1', $employee);

        $employee = new Employee();
        $employee->setCode('12345');
        $employee->setName('Jorge');
        $employee->setLastName('Acosta Ramirez');
        $employee->setPhone('8182585859');
        $employee->setjobPosition('Collector');
        $manager->persist($employee);
        $manager->flush();
        $this->addReference('employee2', $employee);

        $employee = new Employee();
        $employee->setCode('74589');
        $employee->setName('Maria');
        $employee->setLastName('Velasco Dominguez');
        $employee->setPhone('8182585859');
        $employee->setjobPosition('Messenger');
        $manager->persist($employee);
        $manager->flush();
        $this->addReference('employee3', $employee);

        $employee = new Employee();
        $employee->setCode('3547');
        $employee->setName('Pedro');
        $employee->setLastName('Pereyra Juarez');
        $employee->setPhone('8182585859');
        $employee->setjobPosition('Collector');
        $manager->persist($employee);
        $manager->flush();
        $this->addReference('employee4', $employee);

        $employee = new Employee();
        $employee->setCode('896565');
        $employee->setName('Juan');
        $employee->setLastName('Santos Lopez');
        $employee->setPhone('8182585859');
        $employee->setjobPosition('Messenger');
        $manager->persist($employee);
        $manager->flush();
        $this->addReference('employee5', $employee);

        $employee = new Employee();
        $employee->setCode('41243659');
        $employee->setName('Ana');
        $employee->setLastName('Gonzalez Urrutia');
        $employee->setPhone('8182545893');
        $employee->setjobPosition('Collector');
        $manager->persist($employee);
        $manager->flush();
        $this->addReference('employee6', $employee);

        $employee = new Employee();
        $employee->setCode('123456');
        $employee->setName('Carlos');
        $employee->setLastName('Perez Gomez');
        $employee->setPhone('8182585859');
        $employee->setjobPosition('Messenger');
        $manager->persist($employee);
        $manager->flush();
        $this->addReference('employee7', $employee);

        $employee = new Employee();
        $employee->setCode('12548');
        $employee->setName('Brenda');
        $employee->setLastName('Lopez Ortega');
        $employee->setPhone('818575775');
        $employee->setjobPosition('Messenger');
        $manager->persist($employee);
        $manager->flush();
        $this->addReference('employee8', $employee);

        $employee = new Employee();
        $employee->setCode('123456');
        $employee->setName('Ruben');
        $employee->setLastName('Carrizales Dominguez');
        $employee->setPhone('8182585859');
        $employee->setjobPosition('Collector');
        $manager->persist($employee);
        $manager->flush();
        $this->addReference('employee9', $employee);

        $employee = new Employee();
        $employee->setCode('3514935');
        $employee->setName('Jorge');
        $employee->setLastName('Sanchez Lopez');
        $employee->setPhone('8182585859');
        $employee->setjobPosition('Messenger');
        $manager->persist($employee);
        $manager->flush();
        $this->addReference('employee10', $employee);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
