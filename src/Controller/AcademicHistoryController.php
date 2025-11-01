<?php

namespace App\Controller;

use App\Entity\AcademicHistory;
use App\Form\AcademicHistoryType;
use App\Repository\AcademicHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/academic-histories')]
#[IsGranted('ROLE_ADMIN')]
class AcademicHistoryController extends AbstractController
{
    #[Route('/', name: 'app_academic_history_index', methods: ['GET'])]
    public function index(AcademicHistoryRepository $academicHistoryRepository): Response
    {
        return $this->render('admin/academic_history/index.html.twig', [
            'academic_histories' => $academicHistoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_academic_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $academicHistory = new AcademicHistory();
        $form = $this->createForm(AcademicHistoryType::class, $academicHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($academicHistory);
            $entityManager->flush();

            return $this->redirectToRoute('app_academic_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/academic_history/new.html.twig', [
            'academic_history' => $academicHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_academic_history_show', methods: ['GET'])]
    public function show(AcademicHistory $academicHistory): Response
    {
        return $this->render('admin/academic_history/show.html.twig', [
            'academic_history' => $academicHistory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_academic_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AcademicHistory $academicHistory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AcademicHistoryType::class, $academicHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_academic_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/academic_history/edit.html.twig', [
            'academic_history' => $academicHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_academic_history_delete', methods: ['POST'])]
    public function delete(Request $request, AcademicHistory $academicHistory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$academicHistory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($academicHistory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_academic_history_index', [], Response::HTTP_SEE_OTHER);
    }
}
