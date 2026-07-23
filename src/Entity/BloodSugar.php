<?php

namespace App\Entity;

use App\Repository\BloodSugarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BloodSugarRepository::class)]
class BloodSugar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $value = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $time = null;

    #[ORM\Column(length: 255)]
    private ?string $relation = null; // stocke la classification: hypoglycemie / normale / hyperglycemie

    #[ORM\ManyToOne(inversedBy: 'bloodSugars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $patient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

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

    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    public function setTime(\DateTime $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function setRelation(string $relation): static
    {
        $this->relation = $relation;

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
     * Calcule et enregistre automatiquement la classification
     * selon les seuils standards (à jeun) :
     *  - Hypoglycémie  : < 70 mg/dL
     *  - Normale       : 70–110 mg/dL
     *  - Hyperglycémie : > 110 mg/dL
     */
    public function calculerClassification(): string
    {
        $valeur = (float) $this->value;

        if ($valeur <= 70) {
            $classification = 'hypoglycemie';
        } elseif ($valeur <= 110) {
            $classification = 'normale';
        } else {
            $classification = 'hyperglycemie';
        }

        $this->relation = $classification;

        return $classification;
    }
}