<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for(i=0; i<10; i++){
            $user = new User();
            $user->setUsername('User' . $i);
            $user->setPassword('user' . $i);
            $user->setEmail('user' . $i . '@mail.com');
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }

        $admin = new User();
        $admin->setUsername('Admin');
        $admin->setPassword('admin');
        $admin->setEmail('admin@mail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}
