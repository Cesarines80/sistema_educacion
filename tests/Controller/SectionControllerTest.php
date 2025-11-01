<?php

namespace App\Tests\Controller;

use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\Semester;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SectionControllerTest extends WebTestCase
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
        $client->request('GET', '/admin/sections/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Section index');
    }

    public function testNew(): void
    {
        // Create related entities first
        $user = new User();
        $user->setEmail('teacher@example.com');
        $user->setName('Teacher');
        $user->setRoles(['ROLE_TEACHER']);
        $user->setPassword('password');
        $this->entityManager->persist($user);

        $teacher = new Teacher();
        $teacher->setName('Teacher');
        $teacher->setEmail('teacher@example.com');
        $teacher->setUser($user);
        $this->entityManager->persist($teacher);

        $subject = new Subject();
        $subject->setName('Mathematics');
        $subject->setCredits(3);
        $this->entityManager->persist($subject);

        $semester = new Semester();
        $semester->setName('Fall 2023');
        $semester->setStartDate(new \DateTime('2023-09-01'));
        $semester->setEndDate(new \DateTime('2023-12-31'));
        $semester->setIsActive(true);
        $this->entityManager->persist($semester);

        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/sections/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create new Section');

        // Submit form
        $form = $crawler->selectButton('Save')->form([
            'section[name]' => 'Section A',
            'section[capacity]' => 30,
            'section[subject]' => $subject->getId(),
            'section[teacher]' => $teacher->getId(),
            'section[semester]' => $semester->getId(),
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/sections/');
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'Section A');
    }

    public function testShow(): void
    {
        // Create related entities first
        $user = new User();
        $user->setEmail('show.teacher@example.com');
        $user->setName('Show Teacher');
        $user->setRoles(['ROLE_TEACHER']);
        $user->setPassword('password');
        $this->entityManager->persist($user);

        $teacher = new Teacher();
        $teacher->setName('Show Teacher');
        $teacher->setEmail('show.teacher@example.com');
        $teacher->setUser($user);
        $this->entityManager->persist($teacher);

        $subject = new Subject();
        $subject->setName('Physics');
        $subject->setCredits(4);
        $this->entityManager->persist($subject);

        $semester = new Semester();
        $semester->setName('Spring 2023');
        $semester->setStartDate(new \DateTime('2023-01-01'));
        $semester->setEndDate(new \DateTime('2023-05-31'));
        $semester->setIsActive(true);
        $this->entityManager->persist($semester);

        // Create a test section
        $section = new Section();
        $section->setName('Section B');
        $section->setCapacity(25);
        $section->setSubject($subject);
        $section->setTeacher($teacher);
        $section->setSemester($semester);
        $this->entityManager->persist($section);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/admin/sections/' . $section->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Section');
        $this->assertSelectorTextContains('body', 'Section B');
    }

    public function testEdit(): void
    {
        // Create related entities first
        $user = new User();
        $user->setEmail('edit.teacher@example.com');
        $user->setName('Edit Teacher');
        $user->setRoles(['ROLE_TEACHER']);
        $user->setPassword('password');
        $this->entityManager->persist($user);

        $teacher = new Teacher();
        $teacher->setName('Edit Teacher');
        $teacher->setEmail('edit.teacher@example.com');
        $teacher->setUser($user);
        $this->entityManager->persist($teacher);

        $subject = new Subject();
        $subject->setName('Chemistry');
        $subject->setCredits(3);
        $this->entityManager->persist($subject);

        $semester = new Semester();
        $semester->setName('Summer 2023');
        $semester->setStartDate(new \DateTime('2023-06-01'));
        $semester->setEndDate(new \DateTime('2023-08-31'));
        $semester->setIsActive(false);
        $this->entityManager->persist($semester);

        // Create a test section
        $section = new Section();
        $section->setName('Section C');
        $section->setCapacity(20);
        $section->setSubject($subject);
        $section->setTeacher($teacher);
        $section->setSemester($semester);
        $this->entityManager->persist($section);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/sections/' . $section->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Edit Section');

        // Submit form
        $form = $crawler->selectButton('Update')->form([
            'section[name]' => 'Updated Section C',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/sections/' . $section->getId());
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'Updated Section C');
    }

    public function testDelete(): void
    {
        // Create related entities first
        $user = new User();
        $user->setEmail('delete.teacher@example.com');
        $user->setName('Delete Teacher');
        $user->setRoles(['ROLE_TEACHER']);
        $user->setPassword('password');
        $this->entityManager->persist($user);

        $teacher = new Teacher();
        $teacher->setName('Delete Teacher');
        $teacher->setEmail('delete.teacher@example.com');
        $teacher->setUser($user);
        $this->entityManager->persist($teacher);

        $subject = new Subject();
        $subject->setName('Biology');
        $subject->setCredits(2);
        $this->entityManager->persist($subject);

        $semester = new Semester();
        $semester->setName('Winter 2023');
        $semester->setStartDate(new \DateTime('2023-12-01'));
        $semester->setEndDate(new \DateTime('2024-02-28'));
        $semester->setIsActive(false);
        $this->entityManager->persist($semester);

        // Create a test section
        $section = new Section();
        $section->setName('Section D');
        $section->setCapacity(15);
        $section->setSubject($subject);
        $section->setTeacher($teacher);
        $section->setSemester($semester);
        $this->entityManager->persist($section);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/sections/' . $section->getId());

        $this->assertResponseIsSuccessful();

        // Submit delete form
        $form = $crawler->selectButton('Delete')->form();
        $client->submit($form);

        $this->assertResponseRedirects('/admin/sections/');
        $client->followRedirect();

        // Verify section is deleted
        $deletedSection = $this->entityManager->find(Section::class, $section->getId());
        $this->assertNull($deletedSection);
    }
}
