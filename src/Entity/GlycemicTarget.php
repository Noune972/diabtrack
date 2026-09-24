<?php

namespace App\Entity;

use App\Repository\GlycemicTargetRepository;
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
    #[ORM\Column]
    private ?int $fastingMin = null;



    #[ORM\Column]
    private ?int $fastingMax = null;

    #[ORM\Column]
    private ?int $postMealMin = null;

    #[ORM\Column]
    private ?int $postMealMax = null;

    #[ORM\Column]
    private ?int $bedtimeMin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatient(): ?User
    {
        return $this->patient;
    }

    public function getFastingMin(): ?int
{
    return $this->fastingMin;
}

public function setFastingMin(int $fastingMin): static
{
    $this->fastingMin = $fastingMin;

    return $this;
}

public function getFastingMax(): ?int
{
    return $this->fastingMax;
}

public function setFastingMax(int $fastingMax): static
{
    $this->fastingMax = $fastingMax;

    return $this;
}

public function getPostMealMin(): ?int
{
    return $this->postMealMin;
}

public function setPostMealMin(int $postMealMin): static
{
    $this->postMealMin = $postMealMin;

    return $this;
}

public function getPostMealMax(): ?int
{
    return $this->postMealMax;
}

public function setPostMealMax(int $postMealMax): static
{
    $this->postMealMax = $postMealMax;

    return $this;
}

public function getBedtimeMin(): ?int
{
    return $this->bedtimeMin;
}

public function setBedtimeMin(int $bedtimeMin): static
{
    $this->bedtimeMin = $bedtimeMin;

    return $this;
}

#[ORM\Column]
private ?int $bedtimeMax = null;


public function getBedtimeMax(): ?int
{
    return $this->bedtimeMax;
}

public function setBedtimeMax(int $bedtimeMax): static
{
    $this->bedtimeMax = $bedtimeMax;

    return $this;
}


    public function setPatient(User $patient): static
    {
        $this->patient = $patient;

        return $this;
    }
}
