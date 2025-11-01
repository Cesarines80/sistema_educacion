<?php

namespace App\Tests\Controller;

use App\Entity\AcademicHistory;
use App\Entity\Student;
use App\Entity\Semester;
use App\Entity\Subject;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AcademicHistoryControllerTest extends WebTestCase
{
    private $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/admin/academic-histories/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Academic history index');
    }

    public function testNew(): void
    {
        // Create related entities first
        $user = new User();
        $user->setEmail('student@example.com');
        $user->setName('Student');
        $user->setRoles(['ROLE_STUDENT']);
        $user->setPassword('password');
        $user->setRole('ROLE_STUDENT');
        $this->entityManager->persist($user);

        $student = new Student();
        $student->setName('Student');
        $student->setEmail('student@example.com');
        $student->setBirthDate(new \DateTime('2000-01-01'));
        $student->setUser($user);
        $this->entityManager->persist($student);

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

        $crawler = $this->client->request('GET', '/admin/academic-histories/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Create new Academic history');

        // Submit form
        $form = $crawler->selectButton('Save')->form([
            'academic_history[finalGrade]' => '85.50',
            'academic_history[status]' => 'Passed',
            'academic_history[student]' => $student->getId(),
            'academic_history[semester]' => $semester->getId(),
            'academic_history[subject]' => $subject->getId(),
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/academic-histories/');
        $this->client->followRedirect();

        $this->assertSelectorTextContains('body', '85.50');
    }

    public function testShow(): void
    {
        // Create related entities first
        $user = new User();
        $user->setEmail('show.student@example.com');
        $user->setName('Show Student');
        $user->setRoles(['ROLE_STUDENT']);
        $user->setPassword('password');
        $user->setRole('ROLE_STUDENT');
        $this->entityManager->persist($user);

        $student = new Student();
        $student->setName('Show Student');
        $student->setEmail('show.student@example.com');
        $student->setBirthDate(new \DateTime('2000-01-01'));
        $student->setUser($user);
        $this->entityManager->persist($student);

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

        // Create a test academic history
        $academicHistory = new AcademicHistory();
        $academicHistory->setFinalGrade('92.00');
        $academicHistory->setStatus('Passed');
        $academicHistory->setStudent($student);
        $academicHistory->setSemester($semester);
        $academicHistory->setSubject($subject);
        $this->entityManager->persist($academicHistory);
        $this->entityManager->flush();

        $this->client->request('GET', '/admin/academic-histories/' . $academicHistory->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Academic history');
        $this->assertSelectorTextContains('body', '92.00');
    }

    public function testEdit(): void
    {
        // Create related entities first
        $user = new User();
        $user->setEmail('edit.student@example.com');
        $user->setName('Edit Student');
        $user->setRoles(['ROLE_STUDENT']);
        $user->setPassword('password');
        $user->setRole('ROLE_STUDENT');
        $this->entityManager->persist($user);

        $student = new Student();
        $student->setName('Edit Student');
        $student->setEmail('edit.student@example.com');
        $student->setBirthDate(new \DateTime('2000-01-01'));
        $student->setUser($user);
        $this->entityManager->persist($student);

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

        // Create a test academic history
        $academicHistory = new AcademicHistory();
        $academicHistory->setFinalGrade('78.50');
        $academicHistory->setStatus('Failed');
        $academicHistory->setStudent($student);
        $academicHistory->setSemester($semester);
        $academicHistory->setSubject($subject);
        $this->entityManager->persist($academicHistory);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/admin/academic-histories/' . $academicHistory->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Edit Academic history');

        // Submit form
        $form = $crawler->selectButton('Update')->form([
            'academic_history[finalGrade]' => '82.00',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/academic-histories/' . $academicHistory->getId());
        $this->client->followRedirect();

        $this->assertSelectorTextContains('body', '82.00');
    }

    public function testDelete(): void
    {
        // Create related entities first
        $user = new User();
        $user->setEmail('delete.student@example.com');
        $user->setName('Delete Student');
        $user->setRoles(['ROLE_STUDENT']);
        $user->setPassword('password');
        $user->setRole('ROLE_STUDENT');
        $this->entityManager->persist($user);

        $student = new Student();
        $student->setName('Delete Student');
        $student->setEmail('delete.student@example.com');
        $student->setBirthDate(new \DateTime('2000-01-01'));
        $student->setUser($user);
        $this->entityManager->persist($student);

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

        // Create a test academic history
        $academicHistory = new AcademicHistory();
        $academicHistory->setFinalGrade('88.75');
        $academicHistory->setStatus('Passed');
        $academicHistory->setStudent($student);
        $academicHistory->setSemester($semester);
        $academicHistory->setSubject($subject);
        $this->entityManager->persist($academicHistory);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/admin/academic-histories/' . $academicHistory->getId());

        $this->assertResponseIsSuccessful();

        // Submit delete form
        $form = $crawler->selectButton('Delete')->form();
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/academic-histories/');
        $this->client->followRedirect();

        // Verify academic history is deleted
        $deletedAcademicHistory = $this->entityManager->find(AcademicHistory::class, $academicHistory->getId());
        $this->assertNull($deletedAcademicHistory);
    }
}
