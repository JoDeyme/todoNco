<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintsValidator;

class UserTest extends KernelTestCase
{
    public function testEntityUser()
    {
        $user = new User();

        $user->setUsername('Test');
        $user->setPassword('test');
        $user->setEmail('test@mail.com');

        self::bootKernel();

        $error = $this->getContainer()->get('validator')->validate($user);
        $this->assertCount(0, $error);

        $this->assertEquals('Test', $user->getUserIdentifier());
        $this->assertEquals('test', $user->getPassword());
        $this->assertEquals('test@mail.com', $user->getEmail());
    }

    public function testEntityUserUsernameBlank()
    {
        $user = new User();

        $user->setUsername('');
        $user->setPassword('test');
        $user->setEmail('test@mail.com');

        self::bootKernel();

        $error = $this->getContainer()->get('validator')->validate($user);
        $this->assertCount(1, $error);
    }

    public function testEntityUserPasswordBlank()
    {
        $user = new User();

        $user->setUsername('Test');
        $user->setPassword('');
        $user->setEmail('test@mail.com');

        self::bootKernel();

        $error = $this->getContainer()->get('validator')->validate($user);
        $this->assertCount(1, $error);
    }

    public function testEntityUserEmailBlank()
    {
        $user = new User();

        $user->setUsername('Test');
        $user->setPassword('test');
        $user->setEmail('');

        self::bootKernel();

        $error = $this->getContainer()->get('validator')->validate($user);
        $this->assertCount(1, $error);
    }

    public function testEntityUserGetTasks()
    {
        $user = new User();

        $user->setUsername('Test');
        $user->setPassword('test');
        $user->setEmail('test@mail.com');

        $task = new Task();

        $task->setTitle('testTitle');
        $task->setContent('testContent');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setIsDone(false);
        $task->setUser($user);

        $user->addTask($task);

        self::bootKernel();

        $error = $this->getContainer()->get('validator')->validate($user);
        $this->assertCount(0, $error);
        $this->assertEquals('testTitle', $user->getTasks()[0]->getTitle());
        $user->removeTask($task);
        $this->assertCount(0, $user->getTasks());
    }
}
