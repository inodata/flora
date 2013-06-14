<?php
namespace Inodata\FloraBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Application\Sonata\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@test.com');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_SUPER_ADMIN'));

        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user)
        ;
        $user->setPassword($encoder->encodePassword('admin', $user->getSalt()));

        $manager->persist($user);
        $manager->flush();
        $this->addReference('user1', $user);
        
        $user = new User();
        $user->setUsername('o.quiroz');
        $user->setEmail('o.temp@hotmail.com');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_SUPER_ADMIN'));
        
        $encoder = $this->container
        ->get('security.encoder_factory')
        ->getEncoder($user)
        ;
        $user->setPassword($encoder->encodePassword('o.quiroz', $user->getSalt()));
        
        $manager->persist($user);
        $manager->flush();
        $this->addReference('user2', $user);
        
        $user = new User();
        $user->setUsername('n.ramirez');
        $user->setEmail('n.temp@hotmail.com');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_SUPER_ADMIN'));
        
        $encoder = $this->container
        ->get('security.encoder_factory')
        ->getEncoder($user)
        ;
        $user->setPassword($encoder->encodePassword('o.quiroz', $user->getSalt()));
        
        $manager->persist($user);
        $manager->flush();
        $this->addReference('user3', $user);
    }

    /**
     * {@inheritDoc}
     */
     public function getOrder()
     {
        return 5;
     }   
}