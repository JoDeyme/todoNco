<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

// self::ensureKernelShutdown(); à utiliser si le kernel est déjà lancé

class SecurityControllerTest extends WebTestCase
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

    public function testLoginPageIsUp()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }

    //Test d'autentification des utilisateurs

    public function testAuhtenticationBadUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'FAKE', 'password' => 'fake']);
        $this->testClient->submit($form);
        $this->testClient->followRedirect('/');
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testAuhtenticationUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'User0', 'password' => 'user0']);
        $this->testClient->submit($form);
        $this->testClient->followRedirect('/');
        $this->assertSelectorNotExists('.alert.alert-danger');
        $this->assertSelectorNotExists('a:contains("Créer un utilisateur")');
        $this->assertSelectorNotExists('a:contains("Liste des utilisateurs")');
    }

    public function testAuhtenticationAdmin()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'Admin', 'password' => 'admin']);
        $this->testClient->submit($form);
        $this->testClient->followRedirect('/');
        $this->assertSelectorNotExists('.alert.alert-danger');
        $this->assertSelectorExists('a:contains("Créer un utilisateur")');
        $this->assertSelectorExists('a:contains("Liste des utilisateurs")');
    }

    public function testAuhtenticationUserAlreadyConnected()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->loginUser($this->getNormalUser());
        $this->testClient->request('GET', '/login');
        $this->assertResponseRedirects('/');
    }

    //Test de déconnexion

    public function testLogoutUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'User0', 'password' => 'user0']);
        $this->testClient->submit($form);
        $this->testClient->followRedirect('/');
        $this->assertSelectorExists('a:contains("Se déconnecter")');
        $this->assertSelectorNotExists('a:contains("Se connecter")');
        $this->testClient->request('GET', '/logout');
        $this->testClient->followRedirect('/');
        $this->assertSelectorNotExists('a:contains("Se déconnecter")');
        $this->assertSelectorExists('a:contains("Se connecter")');
    }

    public function testLogoutAdmin()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'Admin', 'password' => 'admin']);
        $this->testClient->submit($form);
        $this->testClient->followRedirect('/');
        $this->assertSelectorExists('a:contains("Se déconnecter")');
        $this->assertSelectorNotExists('a:contains("Se connecter")');
        $this->testClient->request('GET', '/logout');
        $this->testClient->followRedirect('/');
        $this->assertSelectorNotExists('a:contains("Se déconnecter")');
        $this->assertSelectorExists('a:contains("Se connecter")');
    }

    //Test de création d'utilisateur

    public function testAdminCreateUser()
    {

        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->loginUser($this->getAdminUser());

        $crawler = $this->testClient->request('GET', '/user/create');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'Usertest',
            'user[password][first]' => 'usertest',
            'user[password][second]' => 'usertest',
            'user[email]' => 'user@mail.com'
        ]);
        $this->testClient->submit($form);
        $this->testClient->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success', 'L\'utilisateur a été bien été ajouté.');
    }

    public function testUserCreateUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'User0', 'password' => 'user0']);
        $this->testClient->submit($form);
        $this->testClient->followRedirect('/');
        $this->testClient->request('GET', '/user/create');
        $this->testClient->followRedirect('/');
        $this->assertSelectorExists('.alert.alert-danger', 'Vous devez être administrateur pour accéder à cette page.');
    }

    public function testNotLoggedCreateUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->request('GET', '/user/create');
        $this->testClient->followRedirect('/');
        $this->assertSelectorExists('.alert.alert-danger', 'Vous devez être connecté pour accéder à cette page.');
    }

    // Test d'édition d'un utilisateur

    public function testAdminEditUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->loginUser($this->getAdminUser());
        $userRepository = static::getContainer()->get(UserRepository::class);
        $id = $userRepository->findOneBy(['username' => 'User0'])->getId();
        $crawler = $this->testClient->request('GET', 'user/' . $id . '/edit');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'UserModif',
            'user[password][first]' => 'usermodif',
            'user[password][second]' => 'usermodif',
            'user[email]' => 'usermodif@mail.com'
        ]);
        $this->testClient->submit($form);
        $this->testClient->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success', 'L\'utilisateur a été bien modifié.');
    }

    public function testUserEditUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->loginUser($this->getNormalUser());
        $userRepository = static::getContainer()->get(UserRepository::class);
        $id = $userRepository->findOneBy(['username' => 'User0'])->getId();
        $this->testClient->request('GET', 'user/' . $id . '/edit');
        $this->testClient->followRedirect('/');
        $this->assertSelectorExists('.alert.alert-danger', 'Vous devez être administrateur pour accéder à cette page.');
    }

    public function testNotLoggedEditUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $id = $userRepository->findOneBy(['username' => 'User0'])->getId();
        $this->testClient->request('GET', 'user/' . $id . '/edit');
        $this->testClient->followRedirect('/');
        $this->assertSelectorExists('.alert.alert-danger', 'Vous devez être connecté pour accéder à cette page.');
    }

    // Test de suppression d'un utilisateur

    public function testAdminDeleteUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->loginUser($this->getAdminUser());
        $userRepository = static::getContainer()->get(UserRepository::class);
        $id = $userRepository->findOneBy(['username' => 'User1'])->getId();
        $this->testClient->request('GET', 'user/' . $id . '/delete');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success', 'L\'utilisateur a été bien supprimé.');
    }

    public function testUserDeleteUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $this->testClient->loginUser($this->getNormalUser());
        $userRepository = static::getContainer()->get(UserRepository::class);
        $id = $userRepository->findOneBy(['username' => 'User0'])->getId();
        $this->testClient->request('GET', 'user/' . $id . '/delete');
        $this->testClient->followRedirect('/');
        $this->assertSelectorExists('.alert.alert-danger', 'Vous devez être administrateur pour accéder à cette page.');
    }

    public function testNotLoggedDeleteUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $id = $userRepository->findOneBy(['username' => 'User0'])->getId();
        $this->testClient->request('GET', 'user/' . $id . '/delete');
        $this->testClient->followRedirect('/');
        $this->assertSelectorExists('.alert.alert-danger', 'Vous devez être administrateur pour accéder à cette page.');
    }

    // Test de la liste des utilisateurs

    public function testAdminListUser()
    {
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'Admin', 'password' => 'admin']);
        $this->testClient->submit($form);
        $this->testClient->followRedirect('/');
        $this->testClient->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1:contains("Liste des utilisateurs")');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
        unset($this->testClient);
    }
}
