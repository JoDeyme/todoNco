<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureInterface
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername('User' . $i);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'user' . $i));
            $user->setEmail('user' . $i . '@mail.com');
            $user->setRoles(['ROLE_USER']);
            $task = new Task();
            $task->setTitle('Task' . $i);
            $task->setContent('Content' . $i);
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->setIsDone(false);
            $task->setUser($user);

            $manager->persist($task);
            $manager->persist($user);
        }
        $admin = new User();
        $admin->setUsername('Admin');
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'admin'));
        $admin->setEmail('admin@mail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}
