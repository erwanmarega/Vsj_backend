<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PerformanceRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PerformanceRepository::class)]
#[ORM\Table(name: "performance")]
class Performance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_performance", type: "integer")]
    #[Groups(['performance:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Swimmer::class)]
    #[ORM\JoinColumn(name: "id_swimmer", referencedColumnName: "id_swimmer", nullable: false, onDelete: "CASCADE")]
    #[Groups(['performance:read', 'performance:write'])]
    private ?Swimmer $swimmer = null;

    #[ORM\Column(name: "historic", type: "string", length: 255)]
    #[Groups(['performance:read', 'performance:write'])]
    private ?string $historic = null;

    #[ORM\ManyToOne(targetEntity: Competition::class)]
    #[ORM\JoinColumn(name: "id_competition", referencedColumnName: "id_competition", nullable: false, onDelete: "CASCADE")]
    #[Groups(['performance:read', 'performance:write'])]
    private ?Competition $competition = null;

    #[ORM\Column(name: "position", type: "integer")]
    #[Groups(['performance:read', 'performance:write'])]
    private ?int $position = null;

    #[ORM\Column(name: "time", type: "integer")]
    #[Groups(['performance:read', 'performance:write'])]
    private ?int $time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSwimmer(): ?Swimmer
    {
        return $this->swimmer;
    }

    public function setSwimmer(?Swimmer $swimmer): self
    {
        $this->swimmer = $swimmer;
        return $this;
    }

    public function getHistoric(): ?string
    {
        return $this->historic;
    }

    public function setHistoric(string $historic): self
    {
        $this->historic = $historic;
        return $this;
    }

    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    public function setCompetition(?Competition $competition): self
    {
        $this->competition = $competition;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;
        return $this;
    }
}
