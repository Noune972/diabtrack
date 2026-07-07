<?php
namespace App\Entity;

use App\Enum\InsulineType;
use App\Repository\InsulineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InsulineRepository::class)]
class Insuline
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: InsulineType::class)]
    private ?InsulineType $type_of_insuline = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column]
    private ?int $hour = null;

    #[ORM\ManyToOne(inversedBy: 'insuline')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $patient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeOfInsuline(): ?InsulineType
    {
        return $this->type_of_insuline;
    }

    public function setTypeOfInsuline(InsulineType $type_of_insuline): static
    {
        $this->type_of_insuline = $type_of_insuline;
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