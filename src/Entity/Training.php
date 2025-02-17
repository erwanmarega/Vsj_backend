<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "training")]
class Training
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_training", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(name: "groups_id", referencedColumnName: "groups_id", nullable: true, onDelete: "SET NULL")]
    private ?Groups $group = null;

    #[ORM\Column(name: "title_training", type: "string", length: 255)]
    private ?string $title = null;

    #[ORM\Column(name: "date_training", type: "datetime")]
    private ?\DateTimeInterface $dateTraining = null;

    #[ORM\Column(name: "duration_training", type: "integer")]
    private ?int $durationTraining = null;

    #[ORM\Column(name: "intensity_training", type: "string", length: 255)]
    private ?string $intensityTraining = null;

    #[ORM\Column(name: "category_training", type: "string", length: 255)]
    private ?string $categoryTraining = null;

    #[ORM\Column(name: "description_training", type: "text", nullable: true)]
    private ?string $descriptionTraining = null;

    #[ORM\Column(name: "is_defined_training", type: "boolean")]
    private bool $isDefinedTraining = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroup(): ?Groups
    {
        return $this->group;
    }

    public function setGroup(?Groups $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDateTraining(): ?\DateTimeInterface
    {
        return $this->dateTraining;
    }

    public function setDateTraining(\DateTimeInterface $dateTraining): self
    {
        $this->dateTraining = $dateTraining;
        return $this;
    }

    public function getDurationTraining(): ?int
    {
        return $this->durationTraining;
    }

    public function setDurationTraining(int $durationTraining): self
    {
        $this->durationTraining = $durationTraining;
        return $this;
    }

    public function getIntensityTraining(): ?string
    {
        return $this->intensityTraining;
    }

    public function setIntensityTraining(string $intensityTraining): self
    {
        $this->intensityTraining = $intensityTraining;
        return $this;
    }

    public function getCategoryTraining(): ?string
    {
        return $this->categoryTraining;
    }

    public function setCategoryTraining(string $categoryTraining): self
    {
        $this->categoryTraining = $categoryTraining;
        return $this;
    }

    public function getDescriptionTraining(): ?string
    {
        return $this->descriptionTraining;
    }

    public function setDescriptionTraining(?string $descriptionTraining): self
    {
        $this->descriptionTraining = $descriptionTraining;
        return $this;
    }

    public function isIsDefinedTraining(): bool
    {
        return $this->isDefinedTraining;
    }

    public function setIsDefinedTraining(bool $isDefinedTraining): self
    {
        $this->isDefinedTraining = $isDefinedTraining;
        return $this;
    }
}
