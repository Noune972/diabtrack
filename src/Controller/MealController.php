<?php

namespace App\Controller;

use App\Entity\AlimentReference;
use App\Entity\Meal;
use App\Entity\MealItem;
use App\Form\MealType;
use App\Repository\MealRepository;
use App\Service\OpenFoodFactsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/meal')]
#[IsGranted('ROLE_USER')]
final class MealController extends AbstractController
{
    #[Route(name: 'app_meal_index', methods: ['GET'])]
    public function index(MealRepository $mealRepository): Response
    {
        $meals = $mealRepository->findBy(
            ['patient' => $this->getUser()],
            ['date' => 'DESC', 'hour' => 'DESC']
        );

        return $this->render('meal/index.html.twig', [
            'meals' => $meals,
        ]);
    }

    #[Route('/new', name: 'app_meal_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $meal = new Meal();
        $now = new \DateTime();

        $meal->setDate($now);
        $meal->setHour($now);

        /*
         * Produit provenant d'Open Food Facts.
         *
         * On récupère :
         * - l'aliment importé
         * - la quantité réellement consommée
         *
         * Puis on crée directement un MealItem.
         */
        if ($request->isMethod('GET')) {
            $alimentId = $request->query->getInt('aliment');
            $quantity = $request->query->get('quantity');

            if ($alimentId > 0) {
                $aliment = $entityManager
                    ->getRepository(AlimentReference::class)
                    ->find($alimentId);

                if ($aliment !== null) {
                    $mealItem = new MealItem();

                    $mealItem->setAlimentReference($aliment);

                    if (
                        $quantity !== null
                        && is_numeric($quantity)
                        && (float) $quantity > 0
                    ) {
                        $mealItem->setQuantity(
                            (float) $quantity
                        );
                    }

                    $meal->addMealItem($mealItem);
                }
            }
        }

        $form = $this->createForm(MealType::class, $meal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $meal->setPatient($this->getUser());

            foreach ($meal->getMealItems() as $mealItem) {
                $mealItem->calculateCalories();
            }

            $meal->updateCalories();

            $entityManager->persist($meal);
            $entityManager->flush();

            $this->addFlash(
                'success',
                sprintf(
                    'Repas enregistré : %s (%d kcal)',
                    $meal->getDishName(),
                    $meal->getCalories()
                )
            );

            return $this->redirectToRoute(
                'app_meal_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('meal/new.html.twig', [
            'meal' => $meal,
            'form' => $form,
        ]);
    }

    /*
     * ================================
     * OPEN FOOD FACTS
     * ================================
     */

    #[Route(
        '/open-food-facts',
        name: 'app_meal_open_food_facts',
        methods: ['GET']
    )]
    public function openFoodFacts(
        Request $request,
        OpenFoodFactsService $openFoodFactsService
    ): Response {
        $barcode = trim(
            (string) $request->query->get('barcode', '')
        );

        $product = null;
        $searched = false;

        if ($barcode !== '') {
            $searched = true;

            $product = $openFoodFactsService->findByBarcode(
                $barcode
            );
        }

        return $this->render(
            'meal/open_food_facts.html.twig',
            [
                'barcode' => $barcode,
                'product' => $product,
                'searched' => $searched,
            ]
        );
    }

    #[Route(
        '/open-food-facts/add',
        name: 'app_meal_open_food_facts_add',
        methods: ['POST']
    )]
    public function addOpenFoodFactsProduct(
        Request $request,
        OpenFoodFactsService $openFoodFactsService,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'add-open-food-facts-product',
            (string) $request->request->get('_token')
        )) {
            $this->addFlash(
                'error',
                'Requête invalide. Merci de réessayer.'
            );

            return $this->redirectToRoute(
                'app_meal_open_food_facts'
            );
        }

        $barcode = trim(
            (string) $request->request->get('barcode', '')
        );

        /*
         * Quantité consommée en grammes.
         */
        $quantityRaw = $request->request->get('quantity');

        if (
            $quantityRaw === null
            || !is_numeric($quantityRaw)
            || (float) $quantityRaw <= 0
        ) {
            $this->addFlash(
                'error',
                'Veuillez indiquer une quantité consommée valide.'
            );

            return $this->redirectToRoute(
                'app_meal_open_food_facts',
                [
                    'barcode' => $barcode,
                ]
            );
        }

        $quantity = (float) $quantityRaw;

        /*
         * On récupère à nouveau le produit depuis l'API.
         *
         * On ne fait pas confiance aux calories envoyées
         * par le navigateur.
         */
        $product = $openFoodFactsService->findByBarcode(
            $barcode
        );

        if ($product === null) {
            $this->addFlash(
                'error',
                'Le produit est introuvable ou ses informations nutritionnelles sont incomplètes.'
            );

            return $this->redirectToRoute(
                'app_meal_open_food_facts',
                [
                    'barcode' => $barcode,
                ]
            );
        }

        /*
         * Recherche du produit dans les aliments DiabTrack.
         */
        $aliment = $entityManager
            ->getRepository(AlimentReference::class)
            ->findOneBy([
                'name' => $product['name'],
                'energieKcal100g' => $product['energyKcal100g'],
            ]);

        /*
         * S'il n'existe pas encore, on l'importe.
         */
        if ($aliment === null) {
            $aliment = new AlimentReference();

            $aliment->setName(
                $product['name']
            );

            $aliment->setEnergieKcal100g(
                $product['energyKcal100g']
            );

            $entityManager->persist($aliment);
            $entityManager->flush();
        }

        /*
         * Calcul uniquement pour le message utilisateur.
         *
         * Le véritable calcul sera refait par MealItem
         * lors de l'enregistrement du repas.
         */
        $calories = round(
            (
                $product['energyKcal100g']
                * $quantity
            ) / 100,
            1
        );

        $this->addFlash(
            'success',
            sprintf(
                '« %s » ajouté : %s g, soit environ %s kcal.',
                $aliment->getName(),
                $quantity,
                $calories
            )
        );

        /*
         * Retour au formulaire du repas avec :
         *
         * aliment  = AlimentReference
         * quantity = grammes consommés
         */
        return $this->redirectToRoute(
            'app_meal_new',
            [
                'aliment' => $aliment->getId(),
                'quantity' => $quantity,
            ]
        );
    }

    /*
     * ================================
     * REPAS
     * ================================
     */

    #[Route(
        '/{id}',
        name: 'app_meal_show',
        methods: ['GET']
    )]
    public function show(Meal $meal): Response
    {
        return $this->render(
            'meal/show.html.twig',
            [
                'meal' => $meal,
            ]
        );
    }

    #[Route(
        '/{id}/edit',
        name: 'app_meal_edit',
        methods: ['GET', 'POST']
    )]
    public function edit(
        Request $request,
        Meal $meal,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(
            MealType::class,
            $meal
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($meal->getMealItems() as $mealItem) {
                $mealItem->calculateCalories();
            }

            $meal->updateCalories();

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Repas mis à jour.'
            );

            return $this->redirectToRoute(
                'app_meal_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render(
            'meal/edit.html.twig',
            [
                'meal' => $meal,
                'form' => $form,
            ]
        );
    }

    #[Route(
        '/{id}/delete',
        name: 'app_meal_delete',
        methods: ['POST']
    )]
    public function delete(
        Request $request,
        Meal $meal,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $meal->getId(),
            $request->getPayload()->getString('_token')
        )) {
            $entityManager->remove($meal);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_meal_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}