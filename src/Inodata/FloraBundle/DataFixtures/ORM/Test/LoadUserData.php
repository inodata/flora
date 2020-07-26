<?php

namespace Inodata\FloraBundle\DataFixtures\ORM;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@test.com');
        $user->setEnabled(true);
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user);
        $user->setPassword($encoder->encodePassword('admin', $user->getSalt()));

        $manager->persist($user);
        $manager->flush();
        $this->addReference('user1', $user);

        $user = new User();
        $user->setUsername('j.ramirez');
        $user->setEmail('j.ramirez@test.com');
        $user->setEnabled(true);
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user);
        $user->setPassword($encoder->encodePassword('j.ramirez', $user->getSalt()));

        $manager->persist($user);
        $manager->flush();
        $this->addReference('user2', $user);

        $user = new User();
        $user->setUsername('r.perez');
        $user->setEmail('r.perez@test.com');
        $user->setEnabled(true);
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user);
        $user->setPassword($encoder->encodePassword('r.perez', $user->getSalt()));

        $manager->persist($user);
        $manager->flush();
        $this->addReference('user3', $user);

        $user = new User();
        $user->setUsername('j.juarez');
        $user->setEmail('j.juarez@test.com');
        $user->setEnabled(true);
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user);
        $user->setPassword($encoder->encodePassword('j.juarez', $user->getSalt()));

        $manager->persist($user);
        $manager->flush();
        $this->addReference('user4', $user);

        $user = new User();
        $user->setUsername('m.carrizales');
        $user->setEmail('m.carrizales@test.com');
        $user->setEnabled(true);
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user);
        $user->setPassword($encoder->encodePassword('m.carrizales', $user->getSalt()));

        $manager->persist($user);
        $manager->flush();
        $this->addReference('user5', $user);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 5;
    }
}
