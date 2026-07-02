<?php

namespace App\Controller;

use App\Entity\SportingActivity;
use App\Form\SportingActivityType;
use App\Repository\SportingActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sporting/activity')]
final class SportingActivityController extends AbstractController
{
    #[Route(name: 'app_sporting_activity_index', methods: ['GET'])]
    public function index(SportingActivityRepository $sportingActivityRepository): Response
    {
        return $this->render('sporting_activity/index.html.twig', [
            'sporting_activities' => $sportingActivityRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sporting_activity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sportingActivity = new SportingActivity();
        $form = $this->createForm(SportingActivityType::class, $sportingActivity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sportingActivity);
            $entityManager->flush();

            return $this->redirectToRoute('app_sporting_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sporting_activity/new.html.twig', [
            'sporting_activity' => $sportingActivity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sporting_activity_show', methods: ['GET'])]
    public function show(SportingActivity $sportingActivity): Response
    {
        return $this->render('sporting_activity/show.html.twig', [
            'sporting_activity' => $sportingActivity,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sporting_activity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SportingActivity $sportingActivity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SportingActivityType::class, $sportingActivity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sporting_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sporting_activity/edit.html.twig', [
            'sporting_activity' => $sportingActivity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sporting_activity_delete', methods: ['POST'])]
    public function delete(Request $request, SportingActivity $sportingActivity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sportingActivity->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sportingActivity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sporting_activity_index', [], Response::HTTP_SEE_OTHER);
    }
}
