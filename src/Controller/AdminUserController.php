<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AdminUserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_admin_user')]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $userRepository->findAll();

        return $this->render('admin_user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}/suspendre', name: 'admin_user_suspend', methods: ['POST'])]
    public function suspend(
        User $user,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid(
            'suspend-user-' . $user->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $user->setIsActive(false);

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le compte de ' . $user->getFirstname() . ' a été suspendu.'
        );

        return $this->redirectToRoute('app_admin_user');
    }

    #[Route('/admin/user/{id}/reactiver', name: 'admin_user_activate', methods: ['POST'])]
    public function activate(
        User $user,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid(
            'activate-user-' . $user->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $user->setIsActive(true);

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le compte de ' . $user->getFirstname() . ' a été réactivé.'
        );

        return $this->redirectToRoute('app_admin_user');
    }
}
