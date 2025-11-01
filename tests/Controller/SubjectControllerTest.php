<?php

namespace App\Tests\Controller;

use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SubjectControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Drop and create database schema for isolation
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $schemaTool->dropSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
    }

    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/subjects/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Subject index');
    }

    public function testNew(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/subjects/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create new Subject');

        // Submit form
        $form = $crawler->selectButton('Save')->form([
            'subject[name]' => 'Mathematics',
            'subject[description]' => 'Introduction to Mathematics',
            'subject[credits]' => 3,
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/subjects/');
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'Mathematics');
    }

    public function testShow(): void
    {
        // Create a test subject
        $subject = new Subject();
        $subject->setName('Physics');
        $subject->setDescription('Introduction to Physics');
        $subject->setCredits(4);
        $this->entityManager->persist($subject);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/admin/subjects/' . $subject->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Subject');
        $this->assertSelectorTextContains('body', 'Physics');
    }

    public function testEdit(): void
    {
        // Create a test subject
        $subject = new Subject();
        $subject->setName('Chemistry');
        $subject->setDescription('Introduction to Chemistry');
        $subject->setCredits(3);
        $this->entityManager->persist($subject);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/subjects/' . $subject->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Edit Subject');

        // Submit form
        $form = $crawler->selectButton('Update')->form([
            'subject[name]' => 'Updated Chemistry',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/subjects/' . $subject->getId());
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'Updated Chemistry');
    }

    public function testDelete(): void
    {
        // Create a test subject
        $subject = new Subject();
        $subject->setName('Biology');
        $subject->setDescription('Introduction to Biology');
        $subject->setCredits(2);
        $this->entityManager->persist($subject);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/subjects/' . $subject->getId());

        $this->assertResponseIsSuccessful();

        // Submit delete form
        $form = $crawler->selectButton('Delete')->form();
        $client->submit($form);

        $this->assertResponseRedirects('/admin/subjects/');
        $client->followRedirect();

        // Verify subject is deleted
        $deletedSubject = $this->entityManager->find(Subject::class, $subject->getId());
        $this->assertNull($deletedSubject);
    }
}
