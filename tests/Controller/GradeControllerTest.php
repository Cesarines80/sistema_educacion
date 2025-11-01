<?php

namespace App\Tests\Controller;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\Semester;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GradeControllerTest extends WebTestCase
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
        $client->request('GET', '/admin/grades/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Grade index');
    }

    public function testNew(): void
    {
        // Create related entities first
        $userStudent = new User();
        $userStudent->setEmail('student@example.com');
        $userStudent->setName('Student');
        $userStudent->setRoles(['ROLE_STUDENT']);
        $userStudent->setPassword('password');
        $this->entityManager->persist($userStudent);

        $student = new Student();
        $student->setName('Student');
        $student->setEmail('student@example.com');
        $student->setUser($userStudent);
        $this->entityManager->persist($student);

        $userTeacher = new User();
        $userTeacher->setEmail('teacher@example.com');
        $userTeacher->setName('Teacher');
        $userTeacher->setRoles(['ROLE_TEACHER']);
        $userTeacher->setPassword('password');
        $this->entityManager->persist($userTeacher);

        $teacher = new Teacher();
        $teacher->setName('Teacher');
        $teacher->setEmail('teacher@example.com');
        $teacher->setUser($userTeacher);
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

        $section = new Section();
        $section->setName('Section A');
        $section->setCapacity(30);
        $section->setSubject($subject);
        $section->setTeacher($teacher);
        $section->setSemester($semester);
        $this->entityManager->persist($section);

        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/grades/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create new Grade');

        // Submit form
        $form = $crawler->selectButton('Save')->form([
            'grade[score]' => '85.50',
            'grade[date]' => '2023-10-01',
            'grade[comments]' => 'Good performance',
            'grade[student]' => $student->getId(),
            'grade[section]' => $section->getId(),
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/grades/');
        $client->followRedirect();

        $this->assertSelectorTextContains('body', '85.50');
    }

    public function testShow(): void
    {
        // Create related entities first
        $userStudent = new User();
        $userStudent->setEmail('show.student@example.com');
        $userStudent->setName('Show Student');
        $userStudent->setRoles(['ROLE_STUDENT']);
        $userStudent->setPassword('password');
        $this->entityManager->persist($userStudent);

        $student = new Student();
        $student->setName('Show Student');
        $student->setEmail('show.student@example.com');
        $student->setUser($userStudent);
        $this->entityManager->persist($student);

        $userTeacher = new User();
        $userTeacher->setEmail('show.teacher@example.com');
        $userTeacher->setName('Show Teacher');
        $userTeacher->setRoles(['ROLE_TEACHER']);
        $userTeacher->setPassword('password');
        $this->entityManager->persist($userTeacher);

        $teacher = new Teacher();
        $teacher->setName('Show Teacher');
        $teacher->setEmail('show.teacher@example.com');
        $teacher->setUser($userTeacher);
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

        $section = new Section();
        $section->setName('Section B');
        $section->setCapacity(25);
        $section->setSubject($subject);
        $section->setTeacher($teacher);
        $section->setSemester($semester);
        $this->entityManager->persist($section);

        // Create a test grade
        $grade = new Grade();
        $grade->setScore('92.00');
        $grade->setDate(new \DateTime('2023-03-15'));
        $grade->setComments('Excellent work');
        $grade->setStudent($student);
        $grade->setSection($section);
        $this->entityManager->persist($grade);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/admin/grades/' . $grade->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Grade');
        $this->assertSelectorTextContains('body', '92.00');
    }

    public function testEdit(): void
    {
        // Create related entities first
        $userStudent = new User();
        $userStudent->setEmail('edit.student@example.com');
        $userStudent->setName('Edit Student');
        $userStudent->setRoles(['ROLE_STUDENT']);
        $userStudent->setPassword('password');
        $this->entityManager->persist($userStudent);

        $student = new Student();
        $student->setName('Edit Student');
        $student->setEmail('edit.student@example.com');
        $student->setUser($userStudent);
        $this->entityManager->persist($student);

        $userTeacher = new User();
        $userTeacher->setEmail('edit.teacher@example.com');
        $userTeacher->setName('Edit Teacher');
        $userTeacher->setRoles(['ROLE_TEACHER']);
        $userTeacher->setPassword('password');
        $this->entityManager->persist($userTeacher);

        $teacher = new Teacher();
        $teacher->setName('Edit Teacher');
        $teacher->setEmail('edit.teacher@example.com');
        $teacher->setUser($userTeacher);
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

        $section = new Section();
        $section->setName('Section C');
        $section->setCapacity(20);
        $section->setSubject($subject);
        $section->setTeacher($teacher);
        $section->setSemester($semester);
        $this->entityManager->persist($section);

        // Create a test grade
        $grade = new Grade();
        $grade->setScore('78.50');
        $grade->setDate(new \DateTime('2023-07-10'));
        $grade->setComments('Needs improvement');
        $grade->setStudent($student);
        $grade->setSection($section);
        $this->entityManager->persist($grade);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/grades/' . $grade->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Edit Grade');

        // Submit form
        $form = $crawler->selectButton('Update')->form([
            'grade[score]' => '82.00',
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/admin/grades/' . $grade->getId());
        $client->followRedirect();

        $this->assertSelectorTextContains('body', '82.00');
    }

    public function testDelete(): void
    {
        // Create related entities first
        $userStudent = new User();
        $userStudent->setEmail('delete.student@example.com');
        $userStudent->setName('Delete Student');
        $userStudent->setRoles(['ROLE_STUDENT']);
        $userStudent->setPassword('password');
        $this->entityManager->persist($userStudent);

        $student = new Student();
        $student->setName('Delete Student');
        $student->setEmail('delete.student@example.com');
        $student->setUser($userStudent);
        $this->entityManager->persist($student);

        $userTeacher = new User();
        $userTeacher->setEmail('delete.teacher@example.com');
        $userTeacher->setName('Delete Teacher');
        $userTeacher->setRoles(['ROLE_TEACHER']);
        $userTeacher->setPassword('password');
        $this->entityManager->persist($userTeacher);

        $teacher = new Teacher();
        $teacher->setName('Delete Teacher');
        $teacher->setEmail('delete.teacher@example.com');
        $teacher->setUser($userTeacher);
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

        $section = new Section();
        $section->setName('Section D');
        $section->setCapacity(15);
        $section->setSubject($subject);
        $section->setTeacher($teacher);
        $section->setSemester($semester);
        $this->entityManager->persist($section);

        // Create a test grade
        $grade = new Grade();
        $grade->setScore('88.75');
        $grade->setDate(new \DateTime('2024-01-15'));
        $grade->setComments('Well done');
        $grade->setStudent($student);
        $grade->setSection($section);
        $this->entityManager->persist($grade);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/grades/' . $grade->getId());

        $this->assertResponseIsSuccessful();

        // Submit delete form
        $form = $crawler->selectButton('Delete')->form();
        $client->submit($form);

        $this->assertResponseRedirects('/admin/grades/');
        $client->followRedirect();

        // Verify grade is deleted
        $deletedGrade = $this->entityManager->find(Grade::class, $grade->getId());
        $this->assertNull($deletedGrade);
    }
}
