<?php

namespace App\Controller;

use App\Entity\Reminder;
use App\Entity\User;
use App\Form\ReminderType;
use App\Repository\ReminderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reminder')]
final class ReminderController extends AbstractController
{
    #[Route(name: 'app_reminder_index', methods: ['GET'])]
    public function index(ReminderRepository $reminderRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('reminder/index.html.twig', [
            'reminders' => $reminderRepository->findForPatient($user),
        ]);
    }

    #[Route('/new', name: 'app_reminder_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $reminder = new Reminder();

        // Le rappel appartient automatiquement
        // à l'utilisateur actuellement connecté.
        $reminder->setPatient($user);

        $form = $this->createForm(ReminderType::class, $reminder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reminder);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre rappel a été créé avec succès.'
            );

            return $this->redirectToRoute(
                'app_reminder_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('reminder/new.html.twig', [
            'reminder' => $reminder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reminder_show', methods: ['GET'])]
    public function show(Reminder $reminder): Response
    {
        $this->checkOwnership($reminder);

        return $this->render('reminder/show.html.twig', [
            'reminder' => $reminder,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reminder_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Reminder $reminder,
        EntityManagerInterface $entityManager
    ): Response {
        $this->checkOwnership($reminder);

        $form = $this->createForm(ReminderType::class, $reminder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre rappel a été modifié avec succès.'
            );

            return $this->redirectToRoute(
                'app_reminder_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('reminder/edit.html.twig', [
            'reminder' => $reminder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reminder_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Reminder $reminder,
        EntityManagerInterface $entityManager
    ): Response {
        $this->checkOwnership($reminder);

        if (
            $this->isCsrfTokenValid(
                'delete' . $reminder->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            $entityManager->remove($reminder);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre rappel a été supprimé.'
            );
        }

        return $this->redirectToRoute(
            'app_reminder_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }

    /**
     * Vérifie que le rappel appartient bien
     * à l'utilisateur actuellement connecté.
     */
    private function checkOwnership(Reminder $reminder): void
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($reminder->getPatient()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas accéder à ce rappel.'
            );
        }
    }
}