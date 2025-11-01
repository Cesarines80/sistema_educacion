<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Create database schema
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
    }

    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/user/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'User index');
    }

    public function testNew(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/user/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create new User');

        // Submit form
        $form = $crawler->selectButton('Save')->form([
            'user[email]' => 'test@example.com',
            'user[password]' => 'password123',
            'user[roles]' => ['ROLE_USER'],
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/user/');
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'test@example.com');
    }

    public function testShow(): void
    {
        // Create a test user
        $user = new User();
        $user->setEmail('show@example.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/admin/user/' . $user->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'User');
        $this->assertSelectorTextContains('body', 'show@example.com');
    }

    public function testEdit(): void
    {
        // Create a test user
        $user = new User();
        $user->setEmail('edit@example.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/user/' . $user->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Edit User');

        // Submit form
        $form = $crawler->selectButton('Update')->form([
            'user[email]' => 'updated@example.com',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/user/' . $user->getId());
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'updated@example.com');
    }

    public function testDelete(): void
    {
        // Create a test user
        $user = new User();
        $user->setEmail('delete@example.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/user/' . $user->getId());

        $this->assertResponseIsSuccessful();

        // Submit delete form
        $form = $crawler->selectButton('Delete')->form();
        $client->submit($form);

        $this->assertResponseRedirects('/admin/user/');
        $client->followRedirect();

        // Verify user is deleted
        $deletedUser = $this->entityManager->find(User::class, $user->getId());
        $this->assertNull($deletedUser);
    }
}
