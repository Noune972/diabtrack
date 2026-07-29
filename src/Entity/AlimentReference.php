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
    #[ORM\OneToMany(targetEntity: MealItem::class, mappedBy: 'alimentReference')]
    private Collection $quantity;

    public function __construct()
    {
        $this->quantity = new ArrayCollection();
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
    public function getQuantity(): Collection
    {
        return $this->quantity;
    }

    public function addQuantity(MealItem $quantity): static
    {
        if (!$this->quantity->contains($quantity)) {
            $this->quantity->add($quantity);
            $quantity->setAlimentReference($this);
        }

        return $this;
    }

    public function removeQuantity(MealItem $quantity): static
    {
        if ($this->quantity->removeElement($quantity)) {
            // set the owning side to null (unless already changed)
            if ($quantity->getAlimentReference() === $this) {
                $quantity->setAlimentReference(null);
            }
        }

        return $this;
    }
}
