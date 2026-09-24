<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
#[IsGranted('ROLE_USER')]
final class NotificationController extends AbstractController
{
    #[Route('', name: 'app_notification_index', methods: ['GET'])]
    public function index(
        NotificationRepository $notificationRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $notifications = $notificationRepository
            ->findLatestForPatient($user, 50);

        $unreadCount = $notificationRepository
            ->countUnreadForPatient($user);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    #[Route(
        '/{id}/lire',
        name: 'app_notification_read',
        methods: ['POST']
    )]
    public function markAsRead(
        Notification $notification,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        /** @var User $user */
        $user = $this->getUser();

        /*
         * Sécurité :
         * impossible de modifier la notification d'un autre patient.
         */
        if ($notification->getPatient() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $notification->setIsRead(true);

        $entityManager->flush();

        return $this->redirectToRoute('app_notification_index');
    }

    #[Route(
        '/tout-lire',
        name: 'app_notification_read_all',
        methods: ['POST']
    )]
    public function markAllAsRead(
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        /** @var User $user */
        $user = $this->getUser();

        $notifications = $notificationRepository
            ->findLatestForPatient($user, 1000);

        foreach ($notifications as $notification) {
            if (!$notification->isRead()) {
                $notification->setIsRead(true);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_notification_index');
    }
}