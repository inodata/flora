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
        $message->setCode('1');
        $message->setMessage('NO SOLO TE DEBO LA VIDA,SI NO TODO LO QUE SOY GRACIAS MADRE.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message1', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('2');
        $message->setMessage('GRACIAS MADRE TU FUERZA Y TU AMOR ME HAN DADO LAS ALAS QUE NECESITABA PARA VOLAR.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message2', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('3');
        $message->setMessage('LOS CONSEJOS DE UNA MADRE SON LOS UNICOS,QUE VIENEN SIEMPRE DESDE EL CORAZON,POR ESO HAN DE SER BIEN RECIBIDOS Feliz dia de las Madres.!!');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message3', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('4');
        $message->setMessage('MADRE:LA PALABRA MAS BELLA PRONUNCIADA,AL SER HUMANO MAS BELLO DEL MUNDO ERES TU. !!');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message4', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('5');
        $message->setMessage('NO HAY SUFICIENTES DIAS AL AÑO, PARA DARTE GRACIAS MADRE,POR TU DEDICACION GENEROSA Y AMOR INCONDICIONAL FELIZ DIA.!!');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message5', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('6');
        $message->setMessage('GRACIAS MADRE TU FUERZA Y TU AMOR ME HAN DADO LAS ALAS QUE NECESITABA PARA VOLAR.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message6', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('7');
        $message->setMessage('HOY TENGO DECIR QUE TE QUIERO PORQUE MANDA EL CALENDARIO,EL RESTO DEL AÑO, TE LO DEMUESTRO PORQUE ERES LA MEJOR MADRE DEL MUNDO');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message7', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('8');
        $message->setMessage('EL AMOR DE UNA MADRE ES EL COMBUSTIBLE QUE LE PERMITE AL SER HUMANO,LOGRAR LO IMPOSIBLE');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message8', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('9');
        $message->setMessage('SOLO UNA MADRE SABE LO QUE ES AMAR A UN HIJO,CON TODOS SUS DEFECTOS Y PROBLEMAS.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message9', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('10');
        $message->setMessage('MAMA TUS BRAZOS SE ABREN CUANDO NECESITO UN ABRAZO,TU CORAZON SABE CUANDO NECESITO UNA AMIGA.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message10', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('11');
        $message->setMessage('PARA TI HERMANA EN ESTE DIA ESPECIAL,GRACIAS POR SER LA MEJOR MADRE DEL MUNDO.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message11', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setCode('12');
        $message->setMessage('GRACIAS POR SER LA MEJOR MADRE,QUE TIENEN MIS HIJOS TE AMO.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message12', $message);
        
        
   
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 8;
    }
}