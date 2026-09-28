<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\CommentArticle;
use App\Entity\User;
use App\Enum\CommentStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\BloodSugar;
use App\Entity\Hba1c;
use App\Entity\Meal;
use App\Entity\SportingActivity;
use App\Entity\Insuline;
use App\Entity\PlatformVisit;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(
        EntityManagerInterface $entityManager
    ): Response {
        // Sécurité supplémentaire :
        // seule une personne ayant ROLE_ADMIN peut accéder à cette page.
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Nombre total d'utilisateurs
        $numberOfUsers = $entityManager
            ->getRepository(User::class)
            ->count([]);

        // Nombre total d'articles
        $numberOfArticles = $entityManager
            ->getRepository(Article::class)
            ->count([]);

        // Nombre total de commentaires
        $numberOfComments = $entityManager
            ->getRepository(CommentArticle::class)
            ->count([]);

        // Commentaires en attente de validation
        $numberOfPendingComments = $entityManager
            ->getRepository(CommentArticle::class)
            ->count([
                'status' => CommentStatus::NON_VALID,
            ]);

            // Nombre total de glycémies enregistrées
$numberOfBloodSugars = $entityManager
    ->getRepository(BloodSugar::class)
    ->count([]);

// Nombre total de mesures HbA1c
$numberOfHba1c = $entityManager
    ->getRepository(Hba1c::class)
    ->count([]);

// Nombre total de repas enregistrés
$numberOfMeals = $entityManager
    ->getRepository(Meal::class)
    ->count([]);

// Nombre total d'activités physiques
$numberOfActivities = $entityManager
    ->getRepository(SportingActivity::class)
    ->count([]);

// Nombre total d'injections d'insuline
$numberOfInsulines = $entityManager
    ->getRepository(Insuline::class)
    ->count([]);
    // Activité de la plateforme sur les 7 derniers jours
$startDate = new \DateTimeImmutable('today -6 days');

$platformVisits = $entityManager
    ->getRepository(PlatformVisit::class)
    ->createQueryBuilder('visit')
    ->where('visit.visitedAt >= :startDate')
    ->setParameter('startDate', $startDate)
    ->orderBy('visit.visitedAt', 'ASC')
    ->getQuery()
    ->getResult();

    $weeklyActivity = [];

for ($i = 0; $i < 7; $i++) {
    $date = $startDate->modify("+{$i} days");

    $weeklyActivity[$date->format('Y-m-d')] = [
        'label' => ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'][(int) $date->format('w')],
        'count' => 0,
    ];
}

foreach ($platformVisits as $visit) {
    $day = $visit->getVisitedAt()->format('Y-m-d');

    if (isset($weeklyActivity[$day])) {
        $weeklyActivity[$day]['count']++;
    }
}

        // Récupération des commentaires en attente
        $pendingComments = $entityManager
            ->getRepository(CommentArticle::class)
            ->findBy(
                [
                    'status' => CommentStatus::NON_VALID,
                ],
                [
                    'date' => 'DESC',
                ]
            );

        return $this->render('admin/index.html.twig', [
            'numberOfUsers' => $numberOfUsers,
            'numberOfArticles' => $numberOfArticles,
            'numberOfComments' => $numberOfComments,
            'numberOfPendingComments' => $numberOfPendingComments,
            'numberOfBloodSugars' => $numberOfBloodSugars,
            'numberOfHba1c' => $numberOfHba1c,
            'numberOfMeals' => $numberOfMeals,
            'numberOfActivities' => $numberOfActivities,
            'numberOfInsulines' => $numberOfInsulines,
            'weeklyActivity' => $weeklyActivity,
            
            'pendingComments' => $pendingComments,
        ]);
    }


    #[Route(
        '/admin/commentaire/{id}/autoriser',
        name: 'admin_comment_approve',
        methods: ['POST']
    )]
    public function approveComment(
        CommentArticle $comment,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $comment->setStatus(CommentStatus::VALID);

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le commentaire a été autorisé.'
        );

        return $this->redirectToRoute('app_admin');
    }


    #[Route(
        '/admin/commentaire/{id}/refuser',
        name: 'admin_comment_refuse',
        methods: ['POST']
    )]
    public function refuseComment(
        CommentArticle $comment,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Pour le moment, un commentaire refusé
        // reste NON_VALID et n'est donc pas affiché sur le blog.
        $comment->setStatus(CommentStatus::REFUSED);

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le commentaire a été refusé.'
        );

        return $this->redirectToRoute('app_admin');
    }
}
