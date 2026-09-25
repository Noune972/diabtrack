<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/account')]
#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    #[Route('', name: 'app_account', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Vos informations personnelles ont été mises à jour.'
            );

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/index.html.twig', [
            'profileForm' => $form,
        ]);
    }

    #[Route('/delete', name: 'app_account_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage,
    ): Response {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('delete-account', $request->request->get('_token'))) {
                $this->addFlash('error', 'Requête invalide, merci de réessayer.');

                return $this->redirectToRoute('app_account_delete');
            }

            $password = $request->request->get('password');

            if (!$passwordHasher->isPasswordValid($user, $password)) {
                $this->addFlash('error', 'Mot de passe incorrect. La suppression a été annulée.');

                return $this->redirectToRoute('app_account_delete');
            }

            // La suppression de l'utilisateur entraîne automatiquement, au niveau base de données,
            // la suppression de TOUTES ses données liées (glycémies, repas, plats, et toute future
            // entité ayant onDelete: 'CASCADE' vers User). Aucune boucle manuelle nécessaire.
            $entityManager->remove($user);
            $entityManager->flush();

            $request->getSession()->invalidate();
            $tokenStorage->setToken(null);

            $this->addFlash(
                'success',
                'Votre compte et toutes vos données ont été définitivement supprimés.'
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('account/delete.html.twig');
    }
}