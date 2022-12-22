<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
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

    public function getNormalUser()

    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneBy(['username' => 'User0']);
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


    public function testTasksList()

    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTasksDoneList()

    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $crawler = $this->testClient->request('GET', '/tasks/done');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(10, $crawler->filter('span', 'glyphicon glyphicon-ok'));
    }

    //Test de la page de création de tâche

    public function testCreateTaskNotLogged()
    {
        $this->testClient->request('GET', '/tasks/create');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', "Vous devez être connecté pour créer une tâche.");
    }

    public function testCreateTaskWithUser()
    {

        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $this->testClient->loginUser($this->getNormalUser());
        $crawler = $this->testClient->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Titre test de la tâche',
            'task[content]' => 'Contenu test de la tâche'
        ]);
        $this->testClient->submit($form);
        $this->testClient->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success', 'La tâche a été bien été ajoutée.');
    }

    public function testCreateTaskWithAdmin()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->loginUser($this->getAdminUser());
        $crawler = $this->testClient->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Titre test de la tâche',
            'task[content]' => 'Contenu test de la tâche'
        ]);
        $this->testClient->submit($form);
        $this->testClient->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success', 'La tâche a été bien été ajoutée.');
    }

    //Test de la page d'édition de tâche

    public function testEditTaskNotLogged()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $crawler = $this->testClient->request('GET', '/tasks');
        $link = $crawler->filter('p:contains("User0")')->siblings()->filter('h4')->filter('a')->Attr('href');

        $this->testClient->request('GET', $link);
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', "Vous devez être connecté pour modifier une tâche.");
    }

    public function testEditTaskWithBadUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $crawler = $this->testClient->request('GET', '/tasks');
        $link = $crawler->filter('p:contains("User0")')->siblings()->filter('h4')->filter('a')->Attr('href');

        $this->testClient->request('GET', $link);
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', "Vous devez être connecté pour modifier une tâche.");
    }

    public function testEditTaskWithUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $this->testClient->loginUser($this->getNormalUser());
        $crawler = $this->testClient->request('GET', '/tasks');
        $link = $crawler->filter('p:contains("User0")')->siblings()->filter('h4')->filter('a')->Attr('href');
        $crawler = $this->testClient->request('GET', $link);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Titre test de la tâche modifiée',
            'task[content]' => 'Contenu test de la tâche modifiée'
        ]);
        $this->testClient->submit($form);
        $this->testClient->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success', 'La tâche a été bien été modifiée.');
    }

    public function testEditTaskWithAdmin()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $this->testClient->loginUser($this->getAdminUser());
        $crawler = $this->testClient->request('GET', '/tasks');
        $link = $crawler->filter('p:contains("User5")')->siblings()->filter('h4')->filter('a')->Attr('href');
        $crawler = $this->testClient->request('GET', $link);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Titre test de la tâche modifiée',
            'task[content]' => 'Contenu test de la tâche modifiée'
        ]);
        $this->testClient->submit($form);
        $this->testClient->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success', 'La tâche a été bien été modifiée.');
    }

    //Test de la page de suppression de tâche

    public function testDeleteTaskNotLogged()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "Task1"])->getId();

        $this->testClient->request('GET', 'tasks/' . $id . '/delete');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', "Vous devez être connecté pour supprimer une tâche.");
    }

    public function testDeleteTaskWithBadUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $this->testClient->loginUser($this->getNormalUser());
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "Task1"])->getId();

        $this->testClient->request('GET', 'tasks/' . $id . '/delete');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', "Seul l'auteur de la tâche peut la supprimer.");
    }

    public function testDeleteTaskWithUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $this->testClient->loginUser($this->getNormalUser());
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "Task0"])->getId();

        $this->testClient->request('GET', 'tasks/' . $id . '/delete');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success', "La tâche a bien été supprimée.");
    }

    public function testDeleteTaskWithAdmin()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $this->testClient->loginUser($this->getAdminUser());
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "Task5"])->getId();

        $this->testClient->request('GET', 'tasks/' . $id . '/delete');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success', "La tâche a bien été supprimée.");
    }

    //Test de la page de marquage d'une tâche comme faite

    public function testToggleTaskNotLogged()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "Task0"])->getId();

        $this->testClient->request('GET', 'tasks/' . $id . '/toggle');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', "Vous devez être connecté pour marquer une tâche comme faite.");
    }

    public function testToggleTaskWithBadUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $this->testClient->loginUser($this->getNormalUser());
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "Task1"])->getId();

        $this->testClient->request('GET', 'tasks/' . $id . '/toggle');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', "Seul l'auteur de la tâche peut la marquer comme faite.");
    }

    public function testToggleTaskWithUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $this->testClient->loginUser($this->getNormalUser());
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "Task0"])->getId();

        $this->testClient->request('GET', 'tasks/' . $id . '/toggle');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success', "La tâche a bien été marquée comme faite.");
    }

    public function testToggleTaskWithAdmin()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);

        $this->testClient->loginUser($this->getAdminUser());
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "Task5"])->getId();

        $this->testClient->request('GET', 'tasks/' . $id . '/toggle');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success', "La tâche a bien été marquée comme faite.");
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
        unset($this->testClient);
    }
}
