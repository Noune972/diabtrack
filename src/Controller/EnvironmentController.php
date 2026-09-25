<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EnvironmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class EnvironmentController extends AbstractController
{
    #[Route(
        '/api/environment',
        name: 'app_environment',
        methods: ['GET']
    )]
    public function environment(
        Request $request,
        EnvironmentService $environmentService
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');

        /*
         * =====================================================
         * 📍 POSITION ACTUELLE
         * =====================================================
         *
         * Si le navigateur nous transmet des coordonnées GPS,
         * elles sont prioritaires.
         */
        if (
            $latitude !== null
            && $longitude !== null
            && is_numeric($latitude)
            && is_numeric($longitude)
        ) {
            $latitude = (float) $latitude;
            $longitude = (float) $longitude;

            if (
                $latitude < -90
                || $latitude > 90
                || $longitude < -180
                || $longitude > 180
            ) {
                return $this->json([
                    'success' => false,
                    'message' => 'Coordonnées géographiques invalides.',
                ], 400);
            }

            $environment = $environmentService->getEnvironment(
                $latitude,
                $longitude
            );

            if ($environment === null) {
                return $this->json([
                    'success' => false,
                    'message' => 'Les données environnementales sont momentanément indisponibles.',
                ], 503);
            }

            $environment['source'] = 'gps';

            return $this->json([
                'success' => true,
                'data' => $environment,
            ]);
        }


        /*
         * =====================================================
         * 🏡 VILLE DU PROFIL
         * =====================================================
         *
         * Sans coordonnées GPS, DiabTrack utilise automatiquement
         * la ville renseignée dans le profil.
         */
        $city = trim((string) $user->getCity());
        $postalCode = trim((string) $user->getPostalCode());

        if ($city === '') {
            return $this->json([
                'success' => false,
                'message' => 'Ajoutez votre ville dans votre profil pour afficher la météo près de chez vous.',
            ], 404);
        }

        $environment = $environmentService->getEnvironmentByCity(
            $city,
            $postalCode !== '' ? $postalCode : null
        );

        if ($environment === null) {
            return $this->json([
                'success' => false,
                'message' => sprintf(
                    'Impossible de trouver les informations météo pour %s.',
                    $city
                ),
            ], 404);
        }

        $environment['source'] = 'profile';

        return $this->json([
            'success' => true,
            'data' => $environment,
        ]);
    }
}