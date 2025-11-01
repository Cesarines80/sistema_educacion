<?php

namespace App\Controller;

use App\Repository\AcademicHistoryRepository;
use App\Repository\GradeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/admin', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/student', name: 'app_student_dashboard')]
    #[IsGranted('ROLE_STUDENT')]
    public function studentDashboard(AcademicHistoryRepository $academicHistoryRepository, GradeRepository $gradeRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $student = $user->getStudent();
        $academicHistories = $academicHistoryRepository->findBy(['student' => $student]);
        $grades = $gradeRepository->findBy(['student' => $student]);

        return $this->render('student/dashboard.html.twig', [
            'academic_histories' => $academicHistories,
            'grades' => $grades,
        ]);
    }
}
