<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('Admin');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'password'));
        $admin->setRoles(['ROLE_ADMIN']);

        $david = new User();
        $david->setUsername('David');
        $david->setPassword($this->passwordEncoder->encodePassword($admin, 'password'));
        $david->setRoles(['ROLE_USER']);

        $users = array($admin, $david);

        for ($x = 0; $x < count($users); $x++) {
            $manager->persist($users[$x]);
        }

        $manager->flush();
    }
}
