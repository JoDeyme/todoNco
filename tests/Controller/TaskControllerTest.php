<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Liiip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class TaskControllerTest extends WebTestCase
{

    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    private $testClient = null;

    public function testSomething()
    {
        $this->assertTrue(true);
    }


    public function testTasksList()

    {

        $client = static::createClient();
        $client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateTaskWithBadUser()
    {


        $client = static::createClient();
        $client->request('GET', '/tasks/create');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', "Vous devez être connecté pour créer une tâche.");
    }

    public function testCreateTaskWithUser()
    {
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([

            'App\DataFixtures\UserFixtures',
            'App\DataFixtures\TaskFixtures',
        ]);

        $client = static::createClient();
        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'Test', 'password' => 'test']);
        $client->submit($form);
        $client->followRedirect();
        $crawler = $client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form[name="task"]');
    }

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
    }
}
