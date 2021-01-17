<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $admin = new User();
        $admin->setEmail("admin@mytube");
        $admin->setName("Administrator");
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, "password"));
        $admin->setRoles([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_USER]);
        $manager->persist($admin);

        $manager->flush();
    }
}
