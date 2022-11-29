<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

// self::ensureKernelShutdown(); à utiliser si le kernel est déjà lancé

class SecurityControllerTest extends WebTestCase
{


    public function testLoginPageIsUp()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(HttpFoundationResponse::HTTP_OK);
    }

    public function testAuhtenticationUser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'Test', 'password' => 'test']);

        $client->submit($form);
        $this->assertSelectorNotExists('.alert.alert-danger');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $crawler = $client->request('GET', '/');
        $this->assertSame(0, $crawler->filter('html:contains("Liste des utilisateurs")')->count());
    }

    public function testAuhtenticationAdmin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'Admin', 'password' => 'admin']);
        $client->submit($form);

        $this->assertSelectorNotExists('.alert.alert-danger');
        $this->assertResponseRedirects();

        $client->followRedirect('/');

        $crawler = $client->request('GET', '/');
        $this->assertSame(1, $crawler->filter('html:contains("Liste des utilisateurs")')->count());
    }

    public function testAuhtenticationBadUser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form(['username' => 'Fake', 'password' => 'fake']);
        $client->submit($form);
        $client->followRedirect('/login');
        $this->assertSelectorExists('.alert.alert-danger');
    }
}
