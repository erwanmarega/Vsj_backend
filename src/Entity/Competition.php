<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "competition")]
class Competition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_competition", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(name: "groups_id", referencedColumnName: "groups_id", nullable: true, onDelete: "SET NULL")]
    private ?Groups $group = null;

    #[ORM\Column(name: "title_competition", type: "string", length: 255)]
    private ?string $title = null;

    #[ORM\Column(name: "day_competition", type: "date")]
    private ?\DateTimeInterface $dayCompetition = null;

    #[ORM\Column(name: "hour_competition", type: "time")]
    private ?\DateTimeInterface $hourCompetition = null;

    #[ORM\Column(name: "duration_competition", type: "integer")]
    private ?int $durationCompetition = null;

    #[ORM\Column(name: "address_competition", type: "string", length: 255)]
    private ?string $addressCompetition = null;

    #[ORM\Column(name: "category_competition", type: "string", length: 255)]
    private ?string $categoryCompetition = null;

    #[ORM\Column(name: "description_competition", type: "text", nullable: true)]
    private ?string $descriptionCompetition = null;

    #[ORM\Column(name: "is_defined_competition", type: "boolean")]
    private bool $isDefinedCompetition = false;

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

    public function getDayCompetition(): ?\DateTimeInterface
    {
        return $this->dayCompetition;
    }

    public function setDayCompetition(\DateTimeInterface $dayCompetition): self
    {
        $this->dayCompetition = $dayCompetition;
        return $this;
    }

    public function getHourCompetition(): ?\DateTimeInterface
    {
        return $this->hourCompetition;
    }

    public function setHourCompetition(\DateTimeInterface $hourCompetition): self
    {
        $this->hourCompetition = $hourCompetition;
        return $this;
    }

    public function getDurationCompetition(): ?int
    {
        return $this->durationCompetition;
    }

    public function setDurationCompetition(int $durationCompetition): self
    {
        $this->durationCompetition = $durationCompetition;
        return $this;
    }

    public function getAddressCompetition(): ?string
    {
        return $this->addressCompetition;
    }

    public function setAddressCompetition(string $addressCompetition): self
    {
        $this->addressCompetition = $addressCompetition;
        return $this;
    }

    public function getCategoryCompetition(): ?string
    {
        return $this->categoryCompetition;
    }

    public function setCategoryCompetition(string $categoryCompetition): self
    {
        $this->categoryCompetition = $categoryCompetition;
        return $this;
    }

    public function getDescriptionCompetition(): ?string
    {
        return $this->descriptionCompetition;
    }

    public function setDescriptionCompetition(?string $descriptionCompetition): self
    {
        $this->descriptionCompetition = $descriptionCompetition;
        return $this;
    }

    public function isIsDefinedCompetition(): bool
    {
        return $this->isDefinedCompetition;
    }

    public function setIsDefinedCompetition(bool $isDefinedCompetition): self
    {
        $this->isDefinedCompetition = $isDefinedCompetition;
        return $this;
    }
}
