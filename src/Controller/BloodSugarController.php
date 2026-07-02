<?php

namespace App\Controller;

use App\Entity\BloodSugar;
use App\Form\BloodSugarType;
use App\Repository\BloodSugarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/blood/sugar')]
final class BloodSugarController extends AbstractController
{
    #[Route(name: 'app_blood_sugar_index', methods: ['GET'])]
    public function index(BloodSugarRepository $bloodSugarRepository): Response
    {
        return $this->render('blood_sugar/index.html.twig', [
            'blood_sugars' => $bloodSugarRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_blood_sugar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bloodSugar = new BloodSugar();
        $form = $this->createForm(BloodSugarType::class, $bloodSugar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bloodSugar);
            $entityManager->flush();

            return $this->redirectToRoute('app_blood_sugar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blood_sugar/new.html.twig', [
            'blood_sugar' => $bloodSugar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blood_sugar_show', methods: ['GET'])]
    public function show(BloodSugar $bloodSugar): Response
    {
        return $this->render('blood_sugar/show.html.twig', [
            'blood_sugar' => $bloodSugar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_blood_sugar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BloodSugar $bloodSugar, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BloodSugarType::class, $bloodSugar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_blood_sugar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blood_sugar/edit.html.twig', [
            'blood_sugar' => $bloodSugar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blood_sugar_delete', methods: ['POST'])]
    public function delete(Request $request, BloodSugar $bloodSugar, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bloodSugar->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($bloodSugar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blood_sugar_index', [], Response::HTTP_SEE_OTHER);
    }
}
