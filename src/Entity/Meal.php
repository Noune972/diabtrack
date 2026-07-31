<?php

namespace App\Entity;

use App\Repository\MealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MealRepository::class)]
class Meal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom du repas ou du plat.
     * Exemple : Déjeuner, Salade César, Poulet rôti...
     */
    #[ORM\Column(length: 255)]
    private ?string $dishName = null;

    /**
     * Calories totales du repas.
     * Elles sont recalculées automatiquement.
     */
    #[ORM\Column]
    private int $calories = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $hour = null;

    #[ORM\ManyToOne(inversedBy: 'meal')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $patient = null;

    /**
     * @var Collection<int, MealItem>
     */
    #[ORM\OneToMany(
        mappedBy: 'meal',
        targetEntity: MealItem::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $mealItems;

    public function __construct()
    {
        $this->mealItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDishName(): ?string
    {
        return $this->dishName;
    }

    public function setDishName(string $dishName): static
    {
        $this->dishName = $dishName;

        return $this;
    }

    public function getCalories(): int
    {
        return $this->calories;
    }

    public function setCalories(int $calories): static
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * Calcule automatiquement les calories du repas.
     */
    public function getTotalCalories(): float
    {
        $total = 0;

        foreach ($this->mealItems as $mealItem) {
            $total += $mealItem->getCalories();
        }

        return round($total, 1);
    }

    /**
     * Met à jour le champ calories.
     */
    public function updateCalories(): void
    {
        $this->calories = (int) round($this->getTotalCalories());
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHour(): ?\DateTimeInterface
    {
        return $this->hour;
    }

    public function setHour(\DateTimeInterface $hour): static
    {
        $this->hour = $hour;

        return $this;
    }

    public function getPatient(): ?User
    {
        return $this->patient;
    }

    public function setPatient(?User $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * @return Collection<int, MealItem>
     */
    public function getMealItems(): Collection
    {
        return $this->mealItems;
    }

    public function addMealItem(MealItem $mealItem): static
    {
        if (!$this->mealItems->contains($mealItem)) {
            $this->mealItems->add($mealItem);
            $mealItem->setMeal($this);
        }

        $this->updateCalories();

        return $this;
    }

    public function removeMealItem(MealItem $mealItem): static
    {
        if ($this->mealItems->removeElement($mealItem)) {
            if ($mealItem->getMeal() === $this) {
                $mealItem->setMeal(null);
            }
        }

        $this->updateCalories();

        return $this;
    }
}