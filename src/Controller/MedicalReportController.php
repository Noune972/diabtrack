<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BloodSugarRepository;
use App\Repository\Hba1cRepository;
use App\Repository\InsulineRepository;
use App\Repository\MealRepository;
use App\Repository\SportingActivityRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rapport-medical')]
#[IsGranted('ROLE_USER')]
final class MedicalReportController extends AbstractController
{
    #[Route('', name: 'app_medical_report', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $period = $request->query->get('period', '30');

        $endDate = new \DateTimeImmutable('today');
        $startDate = $this->getStartDate($period, $endDate);

        return $this->render('medical_report/index.html.twig', [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    #[Route('/telecharger', name: 'app_medical_report_download', methods: ['GET'])]
    public function download(
        Request $request,
        BloodSugarRepository $bloodSugarRepository,
        MealRepository $mealRepository,
        InsulineRepository $insulineRepository,
        SportingActivityRepository $sportingActivityRepository,
        Hba1cRepository $hba1cRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $period = $request->query->get('period', '30');

        $endDate = new \DateTimeImmutable('today');
        $startDate = $this->getStartDate($period, $endDate);

        $bloodSugars = $bloodSugarRepository->findForPatientAndPeriod(
            $user,
            $startDate,
            $endDate
        );

        $meals = $mealRepository->findForPatientAndPeriod(
            $user,
            $startDate,
            $endDate
        );

        $insulines = $insulineRepository->findForPatientAndPeriod(
            $user,
            $startDate,
            $endDate
        );

        $activities = $sportingActivityRepository->findForPatientAndPeriod(
            $user,
            $startDate,
            $endDate
        );

        $hba1cs = $hba1cRepository->findForPatientAndPeriod(
            $user,
            $startDate,
            $endDate
        );

        /*
         * Statistiques glycémie
         */
        $bloodSugarCount = count($bloodSugars);

        $bloodSugarAverage = null;

        $belowTarget = 0;
        $inTarget = 0;
        $aboveTarget = 0;

        if ($bloodSugarCount > 0) {
            $total = 0.0;

            foreach ($bloodSugars as $bloodSugar) {
                $total += (float) $bloodSugar->getValue();

                switch ($bloodSugar->getRelation()) {
                    case 'hypoglycemie':
                        ++$belowTarget;
                        break;

                    case 'hyperglycemie':
                        ++$aboveTarget;
                        break;

                    case 'normale':
                        ++$inTarget;
                        break;
                }
            }

            $bloodSugarAverage = $total / $bloodSugarCount;
        }

        /*
         * Repas
         */
        $totalMealCalories = 0;

        foreach ($meals as $meal) {
            $totalMealCalories += $meal->getCalories();
        }

        /*
         * Insuline
         */
        $totalInsulinDose = 0.0;

        foreach ($insulines as $insuline) {
            $totalInsulinDose += $insuline->getDose() ?? 0;
        }

        /*
         * Activité physique
         */
        $totalActivityDuration = 0;
        $totalActivityCalories = 0;

        foreach ($activities as $activity) {
            $totalActivityDuration += $activity->getDuration() ?? 0;
            $totalActivityCalories += $activity->getCalories() ?? 0;
        }

        /*
         * Dernière HbA1c de la période
         */
        $latestHba1c = !empty($hba1cs)
            ? $hba1cs[array_key_last($hba1cs)]
            : null;

        $html = $this->renderView('medical_report/pdf.html.twig', [
            'user' => $user,

            'startDate' => $startDate,
            'endDate' => $endDate,

            'bloodSugars' => $bloodSugars,
            'bloodSugarCount' => $bloodSugarCount,
            'bloodSugarAverage' => $bloodSugarAverage,
            'belowTarget' => $belowTarget,
            'inTarget' => $inTarget,
            'aboveTarget' => $aboveTarget,

            'meals' => $meals,
            'totalMealCalories' => $totalMealCalories,

            'insulines' => $insulines,
            'totalInsulinDose' => $totalInsulinDose,

            'activities' => $activities,
            'totalActivityDuration' => $totalActivityDuration,
            'totalActivityCalories' => $totalActivityCalories,

            'hba1cs' => $hba1cs,
            'latestHba1c' => $latestHba1c,

            'glycemicTarget' => $user->getGlycemicTarget(),
        ]);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = sprintf(
            'rapport-diabtrack-%s-%s.pdf',
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        );

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]
        );
    }

    private function getStartDate(
        string $period,
        \DateTimeImmutable $endDate
    ): \DateTimeImmutable {
        return match ($period) {
            '7' => $endDate->modify('-6 days'),
            '90' => $endDate->modify('-89 days'),
            default => $endDate->modify('-29 days'),
        };
    }
}