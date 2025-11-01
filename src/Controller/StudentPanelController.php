<?php

namespace App\Controller;

use App\Repository\AcademicHistoryRepository;
use App\Repository\GradeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student/panel')]
#[IsGranted('ROLE_STUDENT')]
class StudentPanelController extends AbstractController
{
    #[Route('/academic-history', name: 'app_student_academic_history', methods: ['GET'])]
    public function academicHistory(AcademicHistoryRepository $academicHistoryRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $student = $user->getStudent();
        $academicHistories = $academicHistoryRepository->findBy(['student' => $student]);

        return $this->render('student/academic_history.html.twig', [
            'academic_histories' => $academicHistories,
        ]);
    }

    #[Route('/grades', name: 'app_student_grades', methods: ['GET'])]
    public function grades(GradeRepository $gradeRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $student = $user->getStudent();
        $grades = $gradeRepository->findBy(['student' => $student]);

        return $this->render('student/grades.html.twig', [
            'grades' => $grades,
        ]);
    }
}
