<?php

namespace App\Entity;

use App\Repository\MealItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MealItemRepository::class)]
class MealItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Meal $meal = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AlimentReference $alimentReference = null;

    /**
     * Quantité consommée en grammes
     */
    #[ORM\Column]
    private ?float $quantity = null;

    /**
     * Calories calculées automatiquement
     */
    #[ORM\Column]
    private float $calories = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMeal(): ?Meal
    {
        return $this->meal;
    }

    public function setMeal(?Meal $meal): static
    {
        $this->meal = $meal;

        return $this;
    }

    public function getAlimentReference(): ?AlimentReference
    {
        return $this->alimentReference;
    }

    public function setAlimentReference(?AlimentReference $alimentReference): static
    {
        $this->alimentReference = $alimentReference;

        // Recalcul automatique si la quantité existe déjà
        $this->calculateCalories();

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        // Recalcul automatique si l'aliment est déjà sélectionné
        $this->calculateCalories();

        return $this;
    }

    public function getCalories(): float
    {
        return $this->calories;
    }

    public function setCalories(float $calories): static
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * Calcule automatiquement les calories
     * Formule :
     * (Calories pour 100 g × quantité) / 100
     */
    public function calculateCalories(): void
    {
        if (
            $this->alimentReference !== null
            && $this->quantity !== null
        ) {
            $this->calories = round(
                ($this->alimentReference->getEnergieKcal100g() * $this->quantity) / 100,
                1
            );
        }
    }
}