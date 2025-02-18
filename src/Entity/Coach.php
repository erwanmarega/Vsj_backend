<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: "`coach`")]
class Coach implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(name: "id_coach", type: "integer")]
private ?int $id = null;

    #[ORM\Column(name: "nom_coach", type: "string", length: 255)]
    private ?string $nom = null;

    #[ORM\Column(name: "prenom_coach", type: "string", length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(name: "tel_coach", type: "string", length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: "email_coach", type: "string", length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: "password_coach", type: "string")]
    private ?string $password = null;

    #[ORM\Column(name: "roles", type: "json")]
    private array $roles = [];

    #[ORM\ManyToMany(targetEntity: Groups::class, inversedBy: "coaches")]
    #[ORM\JoinTable(name: "coach_groups",
        joinColumns: [new ORM\JoinColumn(name: "id_coach", referencedColumnName: "id_coach")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "groups_id", referencedColumnName: "groups_id")]
    )]
    private Collection $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
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

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Groups $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addCoach($this);
        }
        return $this;
    }

    public function removeGroup(Groups $group): self
    {
        if ($this->groups->removeElement($group)) {
            $group->removeCoach($this);
        }
        return $this;
    }
}
