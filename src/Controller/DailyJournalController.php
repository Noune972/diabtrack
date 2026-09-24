<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BloodSugarRepository;
use App\Repository\MealRepository;
use App\Repository\InsulineRepository;
use App\Repository\SportingActivityRepository;
use App\Repository\Hba1cRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DailyJournalController extends AbstractController
{
    #[Route('/ma-journee', name: 'app_daily_journal', methods: ['GET'])]
    public function index(
        Request $request,
        BloodSugarRepository $bloodSugarRepository,
        MealRepository $mealRepository,
        InsulineRepository $insulineRepository,
        SportingActivityRepository $sportingActivityRepository,
        Hba1cRepository $hba1cRepository,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $dateString = $request->query->get('date');

        try {
            $selectedDate = $dateString
                ? new \DateTimeImmutable($dateString)
                : new \DateTimeImmutable('today');
        } catch (\Exception) {
            $selectedDate = new \DateTimeImmutable('today');
        }

        $bloodSugars = $bloodSugarRepository->findForPatientAndDate(
            $user,
            $selectedDate
        );

        $meals = $mealRepository->findForPatientAndDate(
            $user,
            $selectedDate
        );

        $insulines = $insulineRepository->findForPatientAndDate(
            $user,
            $selectedDate
        );

        $activities = $sportingActivityRepository->findForPatientAndDate(
            $user,
            $selectedDate
        );

        $hba1cs = $hba1cRepository->findForPatientAndDate(
            $user,
            $selectedDate
        );

        return $this->render('daily_journal/index.html.twig', [
            'selected_date' => $selectedDate,
            'blood_sugars' => $bloodSugars,
            'meals' => $meals,
            'insulines' => $insulines,
            'activities' => $activities,
            'hba1cs' => $hba1cs,
            'total_events' =>
                count($bloodSugars)
                + count($meals)
                + count($insulines)
                + count($activities)
                + count($hba1cs),
        ]);
    }
}
