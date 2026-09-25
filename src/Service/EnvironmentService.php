<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class EnvironmentService
{
    private const WEATHER_URL = 'https://api.open-meteo.com/v1/forecast';
    private const AIR_QUALITY_URL = 'https://air-quality-api.open-meteo.com/v1/air-quality';
    private const GEOCODING_URL = 'https://geocoding-api.open-meteo.com/v1/search';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Recherche une ville puis récupère son environnement.
     *
     * @return array<string, mixed>|null
     */
    public function getEnvironmentByCity(
        string $city,
        ?string $postalCode = null
    ): ?array {
        $city = trim($city);

        if ($city === '') {
            return null;
        }

        try {
            $search = trim(
                $city . ' ' . ($postalCode ?? '')
            );

            $response = $this->httpClient->request(
                'GET',
                self::GEOCODING_URL,
                [
                    'query' => [
                        'name' => $search,
                        'count' => 1,
                        'language' => 'fr',
                        'format' => 'json',
                    ],
                    'timeout' => 10,
                ]
            );

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $data = $response->toArray(false);

            /*
             * Certains codes postaux peuvent empêcher Open-Meteo
             * de trouver la ville. On retente uniquement avec son nom.
             */
            if (
                empty($data['results'])
                && $postalCode !== null
                && trim($postalCode) !== ''
            ) {
                $response = $this->httpClient->request(
                    'GET',
                    self::GEOCODING_URL,
                    [
                        'query' => [
                            'name' => $city,
                            'count' => 1,
                            'language' => 'fr',
                            'format' => 'json',
                        ],
                        'timeout' => 10,
                    ]
                );

                if ($response->getStatusCode() !== 200) {
                    return null;
                }

                $data = $response->toArray(false);
            }

            $place = $data['results'][0] ?? null;

            if (
                !is_array($place)
                || !isset($place['latitude'], $place['longitude'])
            ) {
                return null;
            }

            $environment = $this->getEnvironment(
                (float) $place['latitude'],
                (float) $place['longitude']
            );

            if ($environment === null) {
                return null;
            }

            /*
             * On ajoute les informations lisibles de localisation
             * aux données environnementales.
             */
            $environment['location'] = [
                'name' => $place['name'] ?? $city,
                'admin1' => $place['admin1'] ?? null,
                'country' => $place['country'] ?? null,
                'postalCode' => $postalCode,
            ];

            return $environment;

        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Récupère les données météo, qualité de l'air et pollens
     * pour des coordonnées géographiques.
     *
     * @return array<string, mixed>|null
     */
    public function getEnvironment(
        float $latitude,
        float $longitude
    ): ?array {
        if (
            $latitude < -90
            || $latitude > 90
            || $longitude < -180
            || $longitude > 180
        ) {
            return null;
        }

        try {
            $weather = $this->getWeather(
                $latitude,
                $longitude
            );

            $airQuality = $this->getAirQuality(
                $latitude,
                $longitude
            );

            if ($weather === null && $airQuality === null) {
                return null;
            }

            return [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'weather' => $weather,
                'airQuality' => $airQuality,
            ];

        } catch (\Throwable) {
            /*
             * Une panne de l'API environnement ne doit jamais
             * empêcher l'utilisateur d'accéder à son dashboard.
             */
            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getWeather(
        float $latitude,
        float $longitude
    ): ?array {
        try {
            $response = $this->httpClient->request(
                'GET',
                self::WEATHER_URL,
                [
                    'query' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude,

                        'current' => implode(',', [
                            'temperature_2m',
                            'apparent_temperature',
                            'relative_humidity_2m',
                            'weather_code',
                            'wind_speed_10m',
                        ]),

                        'daily' => implode(',', [
                            'weather_code',
                            'temperature_2m_max',
                            'temperature_2m_min',
                            'uv_index_max',
                        ]),

                        'timezone' => 'auto',
                        'forecast_days' => 1,
                    ],

                    'timeout' => 10,
                ]
            );

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $data = $response->toArray(false);

            $current = $data['current'] ?? null;
            $daily = $data['daily'] ?? null;

            if (!is_array($current)) {
                return null;
            }

            return [
                'temperature' =>
                    isset($current['temperature_2m'])
                        ? (float) $current['temperature_2m']
                        : null,

                'apparentTemperature' =>
                    isset($current['apparent_temperature'])
                        ? (float) $current['apparent_temperature']
                        : null,

                'humidity' =>
                    isset($current['relative_humidity_2m'])
                        ? (float) $current['relative_humidity_2m']
                        : null,

                'windSpeed' =>
                    isset($current['wind_speed_10m'])
                        ? (float) $current['wind_speed_10m']
                        : null,

                'weatherCode' =>
                    isset($current['weather_code'])
                        ? (int) $current['weather_code']
                        : null,

                'temperatureMax' =>
                    isset($daily['temperature_2m_max'][0])
                        ? (float) $daily['temperature_2m_max'][0]
                        : null,

                'temperatureMin' =>
                    isset($daily['temperature_2m_min'][0])
                        ? (float) $daily['temperature_2m_min'][0]
                        : null,

                'uvIndex' =>
                    isset($daily['uv_index_max'][0])
                        ? (float) $daily['uv_index_max'][0]
                        : null,
            ];

        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getAirQuality(
        float $latitude,
        float $longitude
    ): ?array {
        try {
            $response = $this->httpClient->request(
                'GET',
                self::AIR_QUALITY_URL,
                [
                    'query' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude,

                        'current' => implode(',', [
                            'european_aqi',
                            'pm10',
                            'pm2_5',
                            'alder_pollen',
                            'birch_pollen',
                            'grass_pollen',
                            'mugwort_pollen',
                            'ragweed_pollen',
                        ]),

                        'timezone' => 'auto',
                    ],

                    'timeout' => 10,
                ]
            );

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $data = $response->toArray(false);

            $current = $data['current'] ?? null;

            if (!is_array($current)) {
                return null;
            }

            return [
                'europeanAqi' =>
                    isset($current['european_aqi'])
                        ? (float) $current['european_aqi']
                        : null,

                'pm10' =>
                    isset($current['pm10'])
                        ? (float) $current['pm10']
                        : null,

                'pm25' =>
                    isset($current['pm2_5'])
                        ? (float) $current['pm2_5']
                        : null,

                'pollen' => [
                    'alder' =>
                        isset($current['alder_pollen'])
                            ? (float) $current['alder_pollen']
                            : null,

                    'birch' =>
                        isset($current['birch_pollen'])
                            ? (float) $current['birch_pollen']
                            : null,

                    'grass' =>
                        isset($current['grass_pollen'])
                            ? (float) $current['grass_pollen']
                            : null,

                    'mugwort' =>
                        isset($current['mugwort_pollen'])
                            ? (float) $current['mugwort_pollen']
                            : null,

                    'ragweed' =>
                        isset($current['ragweed_pollen'])
                            ? (float) $current['ragweed_pollen']
                            : null,
                ],
            ];

        } catch (\Throwable) {
            return null;
        }
    }
}