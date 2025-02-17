<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "admin")]
class Admin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_admin", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(name: "groups_id", referencedColumnName: "groups_id", nullable: true, onDelete: "SET NULL")]
    private ?Groups $group = null;

    #[ORM\Column(name: "title_admin", type: "string", length: 255)]
    private ?string $title = null;

    #[ORM\Column(name: "start_date", type: "datetime")]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(name: "duration", type: "integer")]
    private ?int $duration = null;

    #[ORM\Column(name: "intensity", type: "string", length: 255)]
    private ?string $intensity = null;

    #[ORM\Column(name: "category", type: "string", length: 255)]
    private ?string $category = null;

    #[ORM\Column(name: "description", type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "is_defined", type: "boolean")]
    private bool $isDefined = false;

    #[ORM\Column(name: "roles", type: "json")]
    private array $roles = [];

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    public function getIntensity(): ?string
    {
        return $this->intensity;
    }

    public function setIntensity(string $intensity): self
    {
        $this->intensity = $intensity;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function isIsDefined(): bool
    {
        return $this->isDefined;
    }

    public function setIsDefined(bool $isDefined): self
    {
        $this->isDefined = $isDefined;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
}