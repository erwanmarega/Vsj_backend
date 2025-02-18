<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AttendanceRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AttendanceRepository::class)]
#[ORM\Table(name: "attendance")]
class Attendance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_attendance", type: "integer")]
    #[Groups(['attendance:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Swimmer::class)]
    #[ORM\JoinColumn(name: "id_swimmer", referencedColumnName: "id_swimmer", nullable: false, onDelete: "CASCADE")]
    #[Groups(['attendance:read', 'attendance:write'])]
    private ?Swimmer $swimmer = null;

    #[ORM\Column(name: "historic", type: "string", length: 255)]
    #[Groups(['attendance:read', 'attendance:write'])]
    private ?string $historic = null;

    #[ORM\ManyToOne(targetEntity: Training::class)]
    #[ORM\JoinColumn(name: "id_training", referencedColumnName: "id_training", nullable: false, onDelete: "CASCADE")]
    #[Groups(['attendance:read', 'attendance:write'])]
    private ?Training $training = null;

    #[ORM\Column(name: "is_attendance", type: "boolean")]
    #[Groups(['attendance:read', 'attendance:write'])]
    private ?bool $isAttendance = null;

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

    public function getTraining(): ?Training
    {
        return $this->training;
    }

    public function setTraining(?Training $training): self
    {
        $this->training = $training;
        return $this;
    }

    public function isAttendance(): ?bool
    {
        return $this->isAttendance;
    }

    public function setAttendance(bool $isAttendance): self
    {
        $this->isAttendance = $isAttendance;
        return $this;
    }
}
