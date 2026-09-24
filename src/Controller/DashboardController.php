<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BloodSugarRepository;
use App\Repository\MealRepository;
use App\Repository\InsulineRepository;
use App\Repository\SportingActivityRepository;
use App\Repository\Hba1cRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        BloodSugarRepository $bloodSugarRepository,
        MealRepository $mealRepository,
        InsulineRepository $insulineRepository,
        SportingActivityRepository $sportingActivityRepository,
        Hba1cRepository $hba1cRepository,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $today = new \DateTimeImmutable('today');

        // Données de la journée
        $bloodSugarsToday = $bloodSugarRepository->findForPatientAndDate(
            $user,
            $today
        );

        $mealsToday = $mealRepository->findForPatientAndDate(
            $user,
            $today
        );

        $insulinesToday = $insulineRepository->findForPatientAndDate(
            $user,
            $today
        );

        $activitiesToday = $sportingActivityRepository->findForPatientAndDate(
            $user,
            $today
        );

        // Dernière glycémie de la journée
        $lastBloodSugar = !empty($bloodSugarsToday)
            ? $bloodSugarsToday[array_key_last($bloodSugarsToday)]
            : null;

        // Dernier HbA1c enregistré
        $recentHba1cs = $hba1cRepository->findRecentesPourPatient($user, 1);

        $lastHba1c = !empty($recentHba1cs)
            ? $recentHba1cs[0]
            : null;

        // Activité totale aujourd'hui
        $activityDurationToday = 0;
        $activityCaloriesToday = 0;

        foreach ($activitiesToday as $activity) {
            $activityDurationToday += $activity->getDuration() ?? 0;
            $activityCaloriesToday += $activity->getCalories() ?? 0;
        }

        // Calories des repas aujourd'hui
        $mealCaloriesToday = 0;

        foreach ($mealsToday as $meal) {
            $mealCaloriesToday += $meal->getCalories();
        }

        // Dose totale d'insuline aujourd'hui
        $insulineDoseToday = 0.0;

        foreach ($insulinesToday as $insuline) {
            $insulineDoseToday += $insuline->getDose() ?? 0;
        }

        return $this->render('dashboard/index.html.twig', [
            'blood_sugars_today' => $bloodSugarsToday,
            'blood_sugar_count' => count($bloodSugarsToday),
            'last_blood_sugar' => $lastBloodSugar,

            'meals_today' => $mealsToday,
            'meal_count' => count($mealsToday),
            'meal_calories_today' => $mealCaloriesToday,

            'insulines_today' => $insulinesToday,
            'insuline_count' => count($insulinesToday),
            'insuline_dose_today' => $insulineDoseToday,

            'activities_today' => $activitiesToday,
            'activity_count' => count($activitiesToday),
            'activity_duration_today' => $activityDurationToday,
            'activity_calories_today' => $activityCaloriesToday,

            'last_hba1c' => $lastHba1c,
        ]);
    }
}