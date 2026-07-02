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

#[Route('/insuline')]
final class InsulineController extends AbstractController
{
    #[Route(name: 'app_insuline_index', methods: ['GET'])]
    public function index(InsulineRepository $insulineRepository): Response
    {
        return $this->render('insuline/index.html.twig', [
            'insulines' => $insulineRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_insuline_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $insuline = new Insuline();
        $form = $this->createForm(InsulineType::class, $insuline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($insuline);
            $entityManager->flush();

            return $this->redirectToRoute('app_insuline_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('insuline/new.html.twig', [
            'insuline' => $insuline,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_insuline_show', methods: ['GET'])]
    public function show(Insuline $insuline): Response
    {
        return $this->render('insuline/show.html.twig', [
            'insuline' => $insuline,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_insuline_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Insuline $insuline, EntityManagerInterface $entityManager): Response
    {
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
        if ($this->isCsrfTokenValid('delete'.$insuline->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($insuline);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_insuline_index', [], Response::HTTP_SEE_OTHER);
    }
}
