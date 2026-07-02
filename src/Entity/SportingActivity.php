<?php

namespace App\Entity;

use App\Repository\SportingActivityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SportingActivityRepository::class)]
class SportingActivity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type_of_activity = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column]
    private ?int $calories = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $hour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeOfActivity(): ?string
    {
        return $this->type_of_activity;
    }

    public function setTypeOfActivity(string $type_of_activity): static
    {
        $this->type_of_activity = $type_of_activity;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCalories(): ?int
    {
        return $this->calories;
    }

    public function setCalories(int $calories): static
    {
        $this->calories = $calories;

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

    public function getHour(): ?\DateTime
    {
        return $this->hour;
    }

    public function setHour(\DateTime $hour): static
    {
        $this->hour = $hour;

        return $this;
    }
}
