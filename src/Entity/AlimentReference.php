<?php

namespace App\Entity;

use App\Repository\AlimentReferenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlimentReferenceRepository::class)]
class AlimentReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $energieKcal100g = null;

    /**
     * @var Collection<int, MealItem>
     */
    #[ORM\OneToMany(
        mappedBy: 'alimentReference',
        targetEntity: MealItem::class
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEnergieKcal100g(): ?float
    {
        return $this->energieKcal100g;
    }

    public function setEnergieKcal100g(float $energieKcal100g): static
    {
        $this->energieKcal100g = $energieKcal100g;

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
            $mealItem->setAlimentReference($this);
        }

        return $this;
    }

    public function removeMealItem(MealItem $mealItem): static
    {
        if ($this->mealItems->removeElement($mealItem)) {
            if ($mealItem->getAlimentReference() === $this) {
                $mealItem->setAlimentReference(null);
            }
        }

        return $this;
    }
}