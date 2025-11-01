<?php

namespace App\Controller;

use App\Entity\AcademicGrade;
use App\Form\AcademicGradeType;
use App\Repository\AcademicGradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/academic-grade')]
class AcademicGradeController extends AbstractController
{
    #[Route('/', name: 'app_academic_grade_index', methods: ['GET'])]
    public function index(AcademicGradeRepository $academicGradeRepository): Response
    {
        return $this->render('admin/academic_grade/index.html.twig', [
            'academic_grades' => $academicGradeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_academic_grade_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $academicGrade = new AcademicGrade();
        $form = $this->createForm(AcademicGradeType::class, $academicGrade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($academicGrade);
            $entityManager->flush();

            return $this->redirectToRoute('app_academic_grade_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/academic_grade/new.html.twig', [
            'academic_grade' => $academicGrade,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_academic_grade_show', methods: ['GET'])]
    public function show(AcademicGrade $academicGrade): Response
    {
        return $this->render('admin/academic_grade/show.html.twig', [
            'academic_grade' => $academicGrade,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_academic_grade_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AcademicGrade $academicGrade, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AcademicGradeType::class, $academicGrade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_academic_grade_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/academic_grade/edit.html.twig', [
            'academic_grade' => $academicGrade,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_academic_grade_delete', methods: ['POST'])]
    public function delete(Request $request, AcademicGrade $academicGrade, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$academicGrade->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($academicGrade);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_academic_grade_index', [], Response::HTTP_SEE_OTHER);
    }
}
