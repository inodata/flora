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
        $message->setCategory($this->getReference('category2'));
        $message->setMessage('Todos sabemos querer, pero pocos sabemos amar... como yo te AMO');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message1', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category2'));
        $message->setMessage('El amor no tiene limites... cada día te amo más');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message2', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category2'));
        $message->setMessage('El verdadero amor no se conoce por lo que se exige, si no por lo que dá... ¡TE AMO!');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message3', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category3'));
        $message->setMessage('Madre: la palabra más bella pronunciada por el ser humano');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message4', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category1'));
        $message->setMessage('De todos los derechos de una mujer, el más grande es ser madre');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message5', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category4'));
        $message->setMessage('Lo mejor no es tratar al amigo sino al enemigo hacerlo amigo');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message6', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category4'));
        $message->setMessage('Quiero ser tu fortaleza en tu debilidad, quiero ser tu apoyo y contigo poder contar');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message7', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category4'));
        $message->setMessage('Espero que hoy sea solo el comienzo de un año fantástico para tí. Feliz Cumpleaños.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message8', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category4'));
        $message->setMessage('En cada rincon de mi corazon hay algo dedicado a ti, hoy y siempre. Feliz cumpleaños.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message9', $message);
        
        $message = new Message();
        $message->setCategory($this->getReference('category2'));
        $message->setMessage('La amistad nace del corazón y muere persiguiendo un amor.');
        $manager->persist($message);
        $manager->flush();
        $this->addReference('message10', $message);
        
   
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 8;
    }
}