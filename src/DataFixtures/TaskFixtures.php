<?php

namespace App\DataFixtures\TaskFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setTitle('Task' . $i);
            $task->setContent('Content' . $i);
            $task->setCreatedAt(new \DateTime());
            $task->setIsDone(false);
            $task->setUser($this->getReference('user' . $i));

            $manager->persist($task);
        }

        $manager->flush();
    }
}
