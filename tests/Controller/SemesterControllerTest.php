<?php

namespace App\Tests\Controller;

use App\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SemesterControllerTest extends WebTestCase
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
        $client->request('GET', '/admin/semesters/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Semester index');
    }

    public function testNew(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/semesters/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create new Semester');

        // Submit form
        $form = $crawler->selectButton('Save')->form([
            'semester[name]' => 'Fall 2023',
            'semester[startDate]' => '2023-09-01',
            'semester[endDate]' => '2023-12-31',
            'semester[isActive]' => 1,
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/semesters/');
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'Fall 2023');
    }

    public function testShow(): void
    {
        // Create a test semester
        $semester = new Semester();
        $semester->setName('Spring 2023');
        $semester->setStartDate(new \DateTime('2023-01-01'));
        $semester->setEndDate(new \DateTime('2023-05-31'));
        $semester->setIsActive(true);
        $this->entityManager->persist($semester);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/admin/semesters/' . $semester->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Semester');
        $this->assertSelectorTextContains('body', 'Spring 2023');
    }

    public function testEdit(): void
    {
        // Create a test semester
        $semester = new Semester();
        $semester->setName('Summer 2023');
        $semester->setStartDate(new \DateTime('2023-06-01'));
        $semester->setEndDate(new \DateTime('2023-08-31'));
        $semester->setIsActive(false);
        $this->entityManager->persist($semester);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/semesters/' . $semester->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Edit Semester');

        // Submit form
        $form = $crawler->selectButton('Update')->form([
            'semester[name]' => 'Updated Summer 2023',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/semesters/' . $semester->getId());
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'Updated Summer 2023');
    }

    public function testDelete(): void
    {
        // Create a test semester
        $semester = new Semester();
        $semester->setName('Winter 2023');
        $semester->setStartDate(new \DateTime('2023-12-01'));
        $semester->setEndDate(new \DateTime('2024-02-28'));
        $semester->setIsActive(false);
        $this->entityManager->persist($semester);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/semesters/' . $semester->getId());

        $this->assertResponseIsSuccessful();

        // Submit delete form
        $form = $crawler->selectButton('Delete')->form();
        $client->submit($form);

        $this->assertResponseRedirects('/admin/semesters/');
        $client->followRedirect();

        // Verify semester is deleted
        $deletedSemester = $this->entityManager->find(Semester::class, $semester->getId());
        $this->assertNull($deletedSemester);
    }
}
