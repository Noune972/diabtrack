<?php

namespace App\Entity;

use App\Repository\Hba1cRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: Hba1cRepository::class)]
class Hba1c
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // scale: 1 pour conserver la décimale (ex. 5,4 %) - scale: 0 arrondissait à l'entier.
    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 1)]
    #[Assert\NotBlank(message: 'Merci de renseigner le taux d\'HbA1c.')]
    #[Assert\Positive(message: 'Le taux doit être supérieur à 0.')]
    private ?string $value = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column]
    private ?int $hour = null;

    #[ORM\ManyToOne(inversedBy: 'HBA1C')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $patient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value !== null ? (float) $this->value : null;
    }

    public function setValue(float $value): static
    {
        $this->value = (string) $value;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHour(): ?int
    {
        return $this->hour;
    }

    public function setHour(int $hour): static
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
}