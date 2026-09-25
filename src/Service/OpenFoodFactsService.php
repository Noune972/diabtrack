<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenFoodFactsService
{
    private const API_URL = 'https://world.openfoodfacts.org/api/v2/product/';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Recherche un produit Open Food Facts à partir de son code-barres.
     *
     * @return array<string, mixed>|null
     */
    public function findByBarcode(string $barcode): ?array
    {
        $barcode = trim($barcode);

        if ($barcode === '' || !ctype_digit($barcode)) {
            return null;
        }

        try {
            $response = $this->httpClient->request(
                'GET',
                self::API_URL . $barcode,
                [
                    'headers' => [
                        'User-Agent' => 'DiabTrack/1.0',
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'fields' => implode(',', [
                            'code',
                            'product_name',
                            'brands',
                            'image_front_small_url',
                            'nutrition_grades',
                            'nutriments',
                        ]),
                    ],
                    'timeout' => 10,
                ]
            );

            $data = $response->toArray(false);

            if (($data['status'] ?? 0) !== 1) {
                return null;
            }

            $product = $data['product'] ?? null;

            if (!is_array($product)) {
                return null;
            }

            $name = trim((string) ($product['product_name'] ?? ''));

            $energyKcal100g = $product['nutriments']['energy-kcal_100g']
                ?? null;

            if (
                $name === ''
                || $energyKcal100g === null
                || !is_numeric($energyKcal100g)
            ) {
                return null;
            }

            return [
                'barcode' => (string) ($product['code'] ?? $barcode),
                'name' => $name,
                'brand' => $product['brands'] ?? null,
                'energyKcal100g' => (float) $energyKcal100g,
                'nutriscore' => $product['nutrition_grades'] ?? null,
                'image' => $product['image_front_small_url'] ?? null,

                // On les récupère déjà pour pouvoir enrichir
                // AlimentReference plus tard.
                'carbohydrates100g' =>
                    isset($product['nutriments']['carbohydrates_100g'])
                        ? (float) $product['nutriments']['carbohydrates_100g']
                        : null,

                'sugars100g' =>
                    isset($product['nutriments']['sugars_100g'])
                        ? (float) $product['nutriments']['sugars_100g']
                        : null,

                'proteins100g' =>
                    isset($product['nutriments']['proteins_100g'])
                        ? (float) $product['nutriments']['proteins_100g']
                        : null,

                'fat100g' =>
                    isset($product['nutriments']['fat_100g'])
                        ? (float) $product['nutriments']['fat_100g']
                        : null,
            ];
        } catch (\Throwable) {
            // Si Open Food Facts est temporairement indisponible,
            // DiabTrack continue de fonctionner normalement.
            return null;
        }
    }
}
