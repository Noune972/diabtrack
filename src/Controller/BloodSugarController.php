<?php
// src/Controller/BloodSugarController.php
namespace App\Controller;

use App\Entity\BloodSugar;
use App\Form\BloodSugarType;
use App\Repository\BloodSugarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/blood/sugar')]
#[IsGranted('ROLE_USER')]
class BloodSugarController extends AbstractController
{
    #[Route('', name: 'app_blood_sugar_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, BloodSugarRepository $repository): Response
    {
        $bloodSugar = new BloodSugar();
        $now = new \DateTime();
        $bloodSugar->setDate($now);
        $bloodSugar->setTime($now);

        $form = $this->createForm(BloodSugarType::class, $bloodSugar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bloodSugar->setPatient($this->getUser());
            $bloodSugar->calculerClassification();

            $em->persist($bloodSugar);
            $em->flush();

            $this->addFlash('success', 'Mesure enregistrée avec succès.');

            return $this->redirectToRoute('app_blood_sugar_index');
        }

        $bloodSugars = $repository->findBy(
            ['patient' => $this->getUser()],
            ['date' => 'DESC', 'time' => 'DESC']
        );

        return $this->render('blood_sugar/index.html.twig', [
            'form' => $form,
            'blood_sugars' => $bloodSugars,
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
    public function edit(Request $request, BloodSugar $bloodSugar, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BloodSugarType::class, $bloodSugar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bloodSugar->calculerClassification();
            $em->flush();

            $this->addFlash('success', 'Mesure mise à jour.');

            return $this->redirectToRoute('app_blood_sugar_index');
        }

        return $this->render('blood_sugar/edit.html.twig', [
            'blood_sugar' => $bloodSugar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_blood_sugar_delete', methods: ['POST'])]
    public function delete(Request $request, BloodSugar $bloodSugar, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bloodSugar->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($bloodSugar);
            $em->flush();
        }

        return $this->redirectToRoute('app_blood_sugar_index');
    }
}