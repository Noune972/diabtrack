<?php

namespace App\Controller;

use App\Entity\Hba1c;
use App\Form\Hba1cType;
use App\Repository\Hba1cRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/hba1c')]
#[IsGranted('ROLE_USER')]
final class Hba1cController extends AbstractController
{
    #[Route(name: 'app_hba1c_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Hba1cRepository $hba1cRepository, EntityManagerInterface $entityManager): Response
    {
        $hba1c = new Hba1c();
        $hba1c->setPatient($this->getUser());

        $form = $this->createForm(Hba1cType::class, $hba1c);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hba1c);
            $entityManager->flush();

            $this->addFlash('success', "Taux d'HbA1c enregistré.");

            return $this->redirectToRoute('app_hba1c_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hba1c/index.html.twig', [
            // On ne récupère que les mesures du patient connecté, pas toute la table.
            'hba1cs' => $hba1cRepository->findRecentesPourPatient($this->getUser()),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hba1c_show', methods: ['GET'])]
    public function show(Hba1c $hba1c): Response
    {
        $this->denyAccessUnlessOwner($hba1c);

        return $this->render('hba1c/show.html.twig', [
            'hba1c' => $hba1c,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hba1c_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hba1c $hba1c, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessOwner($hba1c);

        $form = $this->createForm(Hba1cType::class, $hba1c);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hba1c_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hba1c/edit.html.twig', [
            'hba1c' => $hba1c,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hba1c_delete', methods: ['POST'])]
    public function delete(Request $request, Hba1c $hba1c, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessOwner($hba1c);

        if ($this->isCsrfTokenValid('delete'.$hba1c->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($hba1c);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hba1c_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Empêche un utilisateur d'accéder, modifier ou supprimer
     * une mesure d'HbA1c qui ne lui appartient pas.
     */
    private function denyAccessUnlessOwner(Hba1c $hba1c): void
    {
        if ($hba1c->getPatient() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    }
}