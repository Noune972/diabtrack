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

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $value = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $time = null;

    #[ORM\Column(length: 255)]
    private ?string $relation = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $context = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'bloodSugars')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
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

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

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
     *  - Hypoglycémie  : < 70 g/L
     *  - Normale       : 70–110 g/L
     *  - Hyperglycémie : > 110 g/L
     */
   public function calculerClassification(
    ?float $minimum = null,
    ?float $maximum = null
): string {
    $valeur = (float) $this->value;

    // Valeurs de repli uniquement si aucun objectif personnalisé
    // n'est encore défini.
    $minimum ??= 0.70;
    $maximum ??= 1.10;

    if ($valeur < $minimum) {
        $classification = 'hypoglycemie';
    } elseif ($valeur > $maximum) {
        $classification = 'hyperglycemie';
    } else {
        $classification = 'normale';
    }

    $this->relation = $classification;

    return $classification;
}
}