<?php

namespace App\Tests\Controller;

use App\Entity\Student;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StudentControllerTest extends WebTestCase
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
        $client->request('GET', '/admin/students/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Student index');
    }

    public function testNew(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/students/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create new Student');

        // Submit form
        $form = $crawler->selectButton('Save')->form([
            'student[name]' => 'John Doe',
            'student[email]' => 'john.doe@example.com',
            'student[phone]' => '1234567890',
            'student[birthDate]' => '2000-01-01',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/students/');
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'john.doe@example.com');
    }

    public function testShow(): void
    {
        // Create a test user first
        $user = new User();
        $user->setEmail('show.student@example.com');
        $user->setName('Show Student');
        $user->setRoles(['ROLE_STUDENT']);
        $user->setPassword('password');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Create a test student
        $student = new Student();
        $student->setName('Show Student');
        $student->setEmail('show.student@example.com');
        $student->setPhone('1234567890');
        $student->setBirthDate(new \DateTime('2000-01-01'));
        $student->setUser($user);
        $this->entityManager->persist($student);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/admin/students/' . $student->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Student');
        $this->assertSelectorTextContains('body', 'show.student@example.com');
    }

    public function testEdit(): void
    {
        // Create a test user first
        $user = new User();
        $user->setEmail('edit.student@example.com');
        $user->setName('Edit Student');
        $user->setRoles(['ROLE_STUDENT']);
        $user->setPassword('password');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Create a test student
        $student = new Student();
        $student->setName('Edit Student');
        $student->setEmail('edit.student@example.com');
        $student->setPhone('1234567890');
        $student->setBirthDate(new \DateTime('2000-01-01'));
        $student->setUser($user);
        $this->entityManager->persist($student);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/students/' . $student->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Edit Student');

        // Submit form
        $form = $crawler->selectButton('Update')->form([
            'student[name]' => 'Updated Student',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/students/' . $student->getId());
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'Updated Student');
    }

    public function testDelete(): void
    {
        // Create a test user first
        $user = new User();
        $user->setEmail('delete.student@example.com');
        $user->setName('Delete Student');
        $user->setRoles(['ROLE_STUDENT']);
        $user->setPassword('password');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Create a test student
        $student = new Student();
        $student->setName('Delete Student');
        $student->setEmail('delete.student@example.com');
        $student->setPhone('1234567890');
        $student->setBirthDate(new \DateTime('2000-01-01'));
        $student->setUser($user);
        $this->entityManager->persist($student);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/students/' . $student->getId());

        $this->assertResponseIsSuccessful();

        // Submit delete form
        $form = $crawler->selectButton('Delete')->form();
        $client->submit($form);

        $this->assertResponseRedirects('/admin/students/');
        $client->followRedirect();

        // Verify student is deleted
        $deletedStudent = $this->entityManager->find(Student::class, $student->getId());
        $this->assertNull($deletedStudent);
    }
}
