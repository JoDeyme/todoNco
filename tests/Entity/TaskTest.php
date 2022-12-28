<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class TaskTest extends KernelTestCase
{


    public function createUser()
    {
        $user = new User();
        $user->setUsername('Test');
        $user->setPassword('test');
        $user->setEmail('test@mail.com');

        return $user;
    }


    public function testEntityTask()
    {
        $task = new Task();

        $task->setTitle('testTitle');
        $task->setContent('testContent');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setIsDone(false);
        $task->setUser($this->createUser());

        self::bootKernel();

        $error = $this->getContainer()->get('validator')->validate($task);
        $this->assertCount(0, $error);

        $this->assertEquals('testTitle', $task->getTitle());
        $this->assertEquals('testContent', $task->getContent());
        $this->assertEquals(false, $task->getIsDone());
        $this->assertEquals('Test', $task->getUser()->getUserIdentifier());
    }

    public function testEntityBlankTaskTitle()
    {
        $task = new Task();

        $task->setTitle('');
        $task->setContent('testContent2');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setIsDone(false);
        $task->setUser($this->createUser());

        self::bootKernel();
        $error = $this->getContainer()->get('validator')->validate($task);
        $this->assertCount(1, $error);
    }

    public function testEntityBlankTaskContent()
    {
        $task = new Task();

        $task->setTitle('testTitle');
        $task->setContent('');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setIsDone(false);
        $task->setUser($this->createUser());

        self::bootKernel();
        $error = $this->getContainer()->get('validator')->validate($task);
        $this->assertCount(1, $error);
    }

    public function testEntityTaskMaxLength()
    {
        $task = new Task();

        $task->setTitle('0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000');
        $task->setContent('00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setIsDone(false);
        $task->setUser($this->createUser());

        self::bootKernel();
        $error = $this->getContainer()->get('validator')->validate($task);
        $this->assertCount(2, $error);
    }

    public function testEntityTaskToggle()
    {
        $task = new Task();

        $task->setTitle('testTitle');
        $task->setContent('testContent');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setIsDone(false);
        $task->setUser($this->createUser());

        self::bootKernel();
        $error = $this->getContainer()->get('validator')->validate($task);
        $this->assertCount(0, $error);
        $this->assertEquals(false, $task->getIsDone());
        $task->toggle(true);
        $this->assertEquals(true, $task->getIsDone());
    }
}
