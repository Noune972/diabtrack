<?php

namespace App\Controller;

use App\Entity\GlycemicTarget;
use App\Entity\User;
use App\Form\GlycemicTargetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mes-objectifs')]
#[IsGranted('ROLE_USER')]
final class GlycemicTargetController extends AbstractController
{
    #[Route('', name: 'app_glycemic_target', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $target = $user->getGlycemicTarget();

        if (!$target) {
            $target = new GlycemicTarget();
            $target->setPatient($user);
        }

        $form = $this->createForm(GlycemicTargetType::class, $target);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $target->setPatient($user);

            $entityManager->persist($target);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Vos objectifs glycémiques ont été enregistrés.'
            );

            return $this->redirectToRoute('app_glycemic_target');
        }

        return $this->render('glycemic_target/index.html.twig', [
            'form' => $form,
            'target' => $target,
        ]);
    }
}