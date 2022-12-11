<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class TaskControllerTest extends WebTestCase
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

    public function testSomething()
    {
        $this->assertTrue(true);
    }


    public function testTasksList()

    {
        $this->databaseTool->loadFixtures([
            'App\DataFixtures\AppFixtures'
        ]);
        $this->testClient->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateTaskWithBadUser()
    {
        $this->testClient->request('GET', '/tasks/create');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', "Vous devez être connecté pour créer une tâche.");
    }

    public function testCreateTaskWithUser()
    {

        $this->databaseTool->loadFixtures([
            'App\DataFixtures\AppFixtures'
        ]);

        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'Admin', 'password' => 'admin']);
        $this->testClient->submit($form);
        $this->testClient->followRedirect();
        $crawler = $this->testClient->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form[name="task"]');
    }
    /*
    public function testCreateTaskWithAdmin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'Admin', 'password' => 'admin']);
        $client->submit($form);
        $client->followRedirect();
        $crawler = $client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form[name="task"]');
    }

    public function testEditTaskWithBadUser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks');
        $link = $crawler->filter('p:contains("Test5")')->siblings()->filter('h4')->filter('a')->Attr('href');

        $client->request('GET', $link);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', "Vous devez être connecté pour modifier une tâche.");
    }


    public function testEditTaskWithUser()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'Test5', 'password' => 'test']);
        $client->submit($form);
        $client->followRedirect();
        $crawler = $client->request('GET', '/tasks');
        $link = $crawler->filter('p:contains("Test5")')->siblings()->filter('h4')->filter('a')->Attr('href');
        $crawler = $client->request('GET', $link);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form[name="task"]');
    } */
}
