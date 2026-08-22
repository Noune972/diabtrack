<?php

namespace App\Controller;

use App\Entity\Insuline;
use App\Form\InsulineType;
use App\Repository\InsulineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/insuline')]
#[IsGranted('ROLE_USER')]
final class InsulineController extends AbstractController
{
    #[Route(name: 'app_insuline_index', methods: ['GET', 'POST'])]
    public function index(Request $request, InsulineRepository $insulineRepository, EntityManagerInterface $entityManager): Response
    {
        $insuline = new Insuline();
        $insuline->setPatient($this->getUser());

        $form = $this->createForm(InsulineType::class, $insuline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($insuline);
            $entityManager->flush();

            $this->addFlash('success', 'Injection enregistrée.');

            return $this->redirectToRoute('app_insuline_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('insuline/index.html.twig', [
            'insulines' => $insulineRepository->findRecentesPourPatient($this->getUser()),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_insuline_show', methods: ['GET'])]
    public function show(Insuline $insuline): Response
    {
        $this->denyAccessUnlessOwner($insuline);

        return $this->render('insuline/show.html.twig', [
            'insuline' => $insuline,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_insuline_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Insuline $insuline, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessOwner($insuline);

        $form = $this->createForm(InsulineType::class, $insuline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_insuline_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('insuline/edit.html.twig', [
            'insuline' => $insuline,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_insuline_delete', methods: ['POST'])]
    public function delete(Request $request, Insuline $insuline, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessOwner($insuline);

        if ($this->isCsrfTokenValid('delete'.$insuline->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($insuline);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_insuline_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Empêche un utilisateur d'accéder, modifier ou supprimer
     * une injection d'insuline qui ne lui appartient pas.
     */
    private function denyAccessUnlessOwner(Insuline $insuline): void
    {
        if ($insuline->getPatient() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    }
}