<?php

namespace App\Controller;

use App\Entity\BloodSugar;
use App\Entity\User;
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
    public function index(
        Request $request,
        EntityManagerInterface $em,
        BloodSugarRepository $repository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $bloodSugar = new BloodSugar();
        $now = new \DateTime();

        $bloodSugar->setDate($now);
        $bloodSugar->setTime($now);
        $bloodSugar->setPatient($user);

        $form = $this->createForm(BloodSugarType::class, $bloodSugar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->classifyBloodSugar($bloodSugar, $user);

            $em->persist($bloodSugar);
            $em->flush();

            $this->addFlash(
                'success',
                'Mesure enregistrée avec succès.'
            );

            return $this->redirectToRoute('app_blood_sugar_index');
        }

        $bloodSugars = $repository->findBy(
            ['patient' => $user],
            ['date' => 'DESC', 'time' => 'DESC']
        );

        return $this->render('blood_sugar/index.html.twig', [
            'form' => $form,
            'blood_sugars' => $bloodSugars,
        ]);
    }

    #[Route('/new', name: 'app_blood_sugar_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $bloodSugar = new BloodSugar();

        $now = new \DateTime();

        $bloodSugar->setDate($now);
        $bloodSugar->setTime($now);
        $bloodSugar->setPatient($user);

        $form = $this->createForm(BloodSugarType::class, $bloodSugar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->classifyBloodSugar($bloodSugar, $user);

            $em->persist($bloodSugar);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre mesure de glycémie a été enregistrée avec succès.'
            );

            return $this->redirectToRoute('app_blood_sugar_index');
        }

        return $this->render('blood_sugar/new.html.twig', [
            'blood_sugar' => $bloodSugar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blood_sugar_show', methods: ['GET'])]
    public function show(BloodSugar $bloodSugar): Response
    {
        $this->checkOwnership($bloodSugar);

        return $this->render('blood_sugar/show.html.twig', [
            'blood_sugar' => $bloodSugar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_blood_sugar_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        BloodSugar $bloodSugar,
        EntityManagerInterface $em
    ): Response {
        $this->checkOwnership($bloodSugar);

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(BloodSugarType::class, $bloodSugar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->classifyBloodSugar($bloodSugar, $user);

            $em->flush();

            $this->addFlash(
                'success',
                'Mesure mise à jour.'
            );

            return $this->redirectToRoute('app_blood_sugar_index');
        }

        return $this->render('blood_sugar/edit.html.twig', [
            'blood_sugar' => $bloodSugar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_blood_sugar_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        BloodSugar $bloodSugar,
        EntityManagerInterface $em
    ): Response {
        $this->checkOwnership($bloodSugar);

        if (
            $this->isCsrfTokenValid(
                'delete'.$bloodSugar->getId(),
                $request->getPayload()->getString('_token')
            )
        ) {
            $em->remove($bloodSugar);
            $em->flush();
        }

        return $this->redirectToRoute('app_blood_sugar_index');
    }

    /**
     * Classe la glycémie selon le contexte et les objectifs
     * personnalisés du patient.
     */
    private function classifyBloodSugar(
        BloodSugar $bloodSugar,
        User $user
    ): void {
        $target = $user->getGlycemicTarget();

        // Si aucun objectif personnalisé n'existe encore,
        // on laisse la méthode de l'entité utiliser son repli.
        if (!$target) {
            $bloodSugar->calculerClassification();

            return;
        }

        $context = $bloodSugar->getContext();

        $beforeMealContexts = [
            'reveil',
            'avant_petit_dejeuner',
            'avant_dejeuner',
            'avant_diner',
        ];

        $afterMealContexts = [
            'apres_petit_dejeuner',
            'apres_dejeuner',
            'apres_diner',
        ];

        if (in_array($context, $beforeMealContexts, true)) {
            $bloodSugar->calculerClassification(
                (float) $target->getFastingMin(),
                (float) $target->getFastingMax()
            );

            return;
        }

        if (in_array($context, $afterMealContexts, true)) {
            $bloodSugar->calculerClassification(
                (float) $target->getPostMealMin(),
                (float) $target->getPostMealMax()
            );

            return;
        }

        if ($context === 'coucher') {
            $bloodSugar->calculerClassification(
                (float) $target->getBedtimeMin(),
                (float) $target->getBedtimeMax()
            );

            return;
        }

        /*
         * Pour les contextes comme :
         * - avant activité
         * - après activité
         * - contrôle
         * - autre
         *
         * on n'invente pas une plage personnalisée qui n'a
         * pas été définie par l'utilisateur.
         */
        $bloodSugar->calculerClassification();
    }

    private function checkOwnership(BloodSugar $bloodSugar): void
    {
        if (
            $bloodSugar->getPatient()?->getId()
            !== $this->getUser()?->getId()
        ) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas accéder à cette mesure.'
            );
        }
    }
}