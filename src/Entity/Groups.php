<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "`groups`")]
class Groups
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "groups_id", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $discipline = null;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDiscipline(): ?string
    {
        return $this->discipline;
    }

    public function setDiscipline(string $discipline): self
    {
        $this->discipline = $discipline;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
