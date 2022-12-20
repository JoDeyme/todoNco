<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{

    /** @var AbstractDatabaseTool */
    protected $databaseTool;
    private $testClient = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->testClient = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function getAdminUser()

    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneBy(['username' => 'Admin']);
    }

    public function testSomething()
    {
        $this->assertTrue(true);
    }

    //Test du nombre d'utilisateurs dans la liste

    public function testUserListCount()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->loginUser($this->getAdminUser());
        $crawler = $this->testClient->request('GET', '/users');
        $this->assertCount(11, $crawler->filter('tbody tr'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
        unset($this->testClient);
    }
}
