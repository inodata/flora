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
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 6;
    }
}