<?php

namespace App\Entity;

use App\Repository\GlycemicTargetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GlycemicTargetRepository::class)]
class GlycemicTarget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'glycemicTarget', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $patient = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $fastingMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $fastingMax = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $postMealMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $postMealMax = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $bedtimeMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $bedtimeMax = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getPatient(): ?User
    {
        return $this->patient;
    }

    public function setPatient(User $patient): static
    {
        $this->patient = $patient;

        return $this;
    }


    public function getFastingMin(): ?string
    {
        return $this->fastingMin;
    }

    public function setFastingMin(string $fastingMin): static
    {
        $this->fastingMin = $fastingMin;

        return $this;
    }


    public function getFastingMax(): ?string
    {
        return $this->fastingMax;
    }

    public function setFastingMax(string $fastingMax): static
    {
        $this->fastingMax = $fastingMax;

        return $this;
    }


    public function getPostMealMin(): ?string
    {
        return $this->postMealMin;
    }

    public function setPostMealMin(string $postMealMin): static
    {
        $this->postMealMin = $postMealMin;

        return $this;
    }


    public function getPostMealMax(): ?string
    {
        return $this->postMealMax;
    }

    public function setPostMealMax(string $postMealMax): static
    {
        $this->postMealMax = $postMealMax;

        return $this;
    }


    public function getBedtimeMin(): ?string
    {
        return $this->bedtimeMin;
    }

    public function setBedtimeMin(string $bedtimeMin): static
    {
        $this->bedtimeMin = $bedtimeMin;

        return $this;
    }


    public function getBedtimeMax(): ?string
    {
        return $this->bedtimeMax;
    }

    public function setBedtimeMax(string $bedtimeMax): static
    {
        $this->bedtimeMax = $bedtimeMax;

        return $this;
    }
}