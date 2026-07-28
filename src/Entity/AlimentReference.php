<?php

namespace App\Entity;

use App\Repository\AlimentReferenceRepository;
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
}
