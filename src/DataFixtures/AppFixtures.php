<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create roles
        $adminRole = new \App\Entity\Role();
        $adminRole->setName('ROLE_ADMIN');
        $manager->persist($adminRole);

        $studentRole = new \App\Entity\Role();
        $studentRole->setName('ROLE_STUDENT');
        $manager->persist($studentRole);

        $teacherRole = new \App\Entity\Role();
        $teacherRole->setName('ROLE_TEACHER');
        $manager->persist($teacherRole);

        // Create admin user
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setName('Admin User');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);

        // Create a sample student user
        $student = new User();
        $student->setEmail('student@example.com');
        $student->setName('Sample Student');
        $student->setRoles(['ROLE_STUDENT']);
        $hashedPassword = $this->passwordHasher->hashPassword($student, 'student123');
        $student->setPassword($hashedPassword);
        $manager->persist($student);

        // Create a sample teacher user
        $teacher = new User();
        $teacher->setEmail('teacher@example.com');
        $teacher->setName('Sample Teacher');
        $teacher->setRoles(['ROLE_TEACHER']);
        $hashedPassword = $this->passwordHasher->hashPassword($teacher, 'teacher123');
        $teacher->setPassword($hashedPassword);
        $manager->persist($teacher);

        $manager->flush();
    }
}
