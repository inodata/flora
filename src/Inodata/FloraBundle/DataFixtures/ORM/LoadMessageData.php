<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inodata\FloraBundle\Entity\Message;

class LoadMessageData extends AbstractFixture implements OrderedFixtureInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('No solo te debo la vida si no todo lo que soy Garcias Madre');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message1', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('Gracias Madre tu fuerza y tu amor me han dado las alas que necesito para volar');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message2', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('Los consejos de una madre son los unicos que vienen siempre desde el corazon por eso han de ser bien recibidos Feliz dia de las Madres !!');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message3', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('Madre la palabra mas bella pronunciada,al ser humano mas bello del mundo eres tu !!');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message4', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('No hay suficientes dias al año para darte gracias Madre por tu dedicacion generosa y amor incondicional feliz dia !!');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message5', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('Hoy tengo decir que te quiero porque manda el calendario,El resto del año, te lo demuestro porque eres la mejor Madre del Mundo ');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message6', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('El amor de una Madre es el combustible que le permite al ser humano,Lograr lo imposible');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message7', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('Solo una madre sabe lo que es amar a un hijo con todos sus defectos y problemas.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message8', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('Mama tus brazos se abren cuando necesito un abrazo,tu corazon sabe cuando necesito una amiga.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message9', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('Para ti hermana en este dia especial,Gracias por ser la mejor madre del mundo.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message10', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('Gracias por ser la mejor madre que tienen mis hijos Te Amo.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message11', $message);
        
        
   
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 8;
    }
}