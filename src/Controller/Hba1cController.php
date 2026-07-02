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

#[Route('/hba1c')]
final class Hba1cController extends AbstractController
{
    #[Route(name: 'app_hba1c_index', methods: ['GET'])]
    public function index(Hba1cRepository $hba1cRepository): Response
    {
        return $this->render('hba1c/index.html.twig', [
            'hba1cs' => $hba1cRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hba1c_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hba1c = new Hba1c();
        $form = $this->createForm(Hba1cType::class, $hba1c);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hba1c);
            $entityManager->flush();

            return $this->redirectToRoute('app_hba1c_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hba1c/new.html.twig', [
            'hba1c' => $hba1c,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hba1c_show', methods: ['GET'])]
    public function show(Hba1c $hba1c): Response
    {
        return $this->render('hba1c/show.html.twig', [
            'hba1c' => $hba1c,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hba1c_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hba1c $hba1c, EntityManagerInterface $entityManager): Response
    {
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
        if ($this->isCsrfTokenValid('delete'.$hba1c->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($hba1c);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hba1c_index', [], Response::HTTP_SEE_OTHER);
    }
}
