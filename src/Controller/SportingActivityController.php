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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sporting/activity')]
#[IsGranted('ROLE_USER')]
final class SportingActivityController extends AbstractController
{
    #[Route(name: 'app_sporting_activity_index', methods: ['GET', 'POST'])]
    public function index(Request $request, SportingActivityRepository $sportingActivityRepository, EntityManagerInterface $entityManager): Response
    {
        $sportingActivity = new SportingActivity();
        $sportingActivity->setPatient($this->getUser());

        $form = $this->createForm(SportingActivityType::class, $sportingActivity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sportingActivity);
            $entityManager->flush();

            $this->addFlash('success', 'Activité physique enregistrée.');

            return $this->redirectToRoute('app_sporting_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sporting_activity/index.html.twig', [
            // On ne récupère que les activités du patient connecté, pas toute la table.
            'sporting_activities' => $sportingActivityRepository->findRecentesPourPatient($this->getUser()),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sporting_activity_show', methods: ['GET'])]
    public function show(SportingActivity $sportingActivity): Response
    {
        $this->denyAccessUnlessOwner($sportingActivity);

        return $this->render('sporting_activity/show.html.twig', [
            'sporting_activity' => $sportingActivity,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sporting_activity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SportingActivity $sportingActivity, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessOwner($sportingActivity);

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
        $this->denyAccessUnlessOwner($sportingActivity);

        if ($this->isCsrfTokenValid('delete'.$sportingActivity->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sportingActivity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sporting_activity_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Empêche un utilisateur d'accéder, modifier ou supprimer
     * une activité physique qui ne lui appartient pas.
     */
    private function denyAccessUnlessOwner(SportingActivity $sportingActivity): void
    {
        if ($sportingActivity->getPatient() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    }
}