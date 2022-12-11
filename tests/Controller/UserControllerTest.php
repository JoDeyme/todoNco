<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{

    /** @var AbstractDatabaseTool */
    protected $databaseTool;


    public function testSomething()
    {
        $this->assertTrue(true);
    }

    public function testUserListCount()
    {
    }
}
