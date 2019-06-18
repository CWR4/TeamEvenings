<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('CWR');
        $userAdmin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $passwordAdmin = $this->encoder->encodePassword($userAdmin, '4');
        $userAdmin->setPassword($passwordAdmin);

        $userDummy = new User();
        $userDummy->setUsername('user1');
        $userDummy->setRoles(['ROLE_USER']);
        $passwordDummy = $this->encoder->encodePassword($userDummy, 'user1');
        $userDummy->setPassword($passwordDummy);

        $manager->persist($userAdmin);
        $manager->persist($userDummy);

        $manager->flush();
    }
}
