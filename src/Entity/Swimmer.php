<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: "swimmer")]
class Swimmer implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_swimmer", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(name: "groups_id", referencedColumnName: "groups_id", nullable: true, onDelete: "SET NULL")]
    private ?Groups $group = null;

    #[ORM\Column(name: "nom_swimmer", type: "string", length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(name: "prenom_swimmer", type: "string", length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(name: "date_naissance_swimmer", type: "date", nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(name: "email_swimmer", type: "string", length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: "roles", type: "json")]
    private array $roles = [];

    #[ORM\Column(name: "password_swimmer", type: "string")]
    private ?string $password = null;

    #[ORM\Column(name: "adresse_swimmer", type: "string", length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: "code_postal_swimmer", type: "string", length: 10, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(name: "ville_swimmer", type: "string", length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(name: "telephone_swimmer", type: "string", length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: "level", type: "integer", nullable: true)]
    private ?int $level = null;

    #[ORM\Column(name: "crawl", type: "integer", nullable: true)]
    private ?int $crawl = null;

    #[ORM\Column(name: "papillon", type: "integer", nullable: true)]
    private ?int $papillon = null;

    #[ORM\Column(name: "dos_crawl", type: "integer", nullable: true)]
    private ?int $dosCrawl = null;

    #[ORM\Column(name: "brasse", type: "integer", nullable: true)]
    private ?int $brasse = null;

     #[ORM\Column(name: "bio", type: "text", nullable: true)]
     private ?string $bio = null;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): self
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;
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

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;
        return $this;
    }

    public function getCrawl(): ?int
    {
        return $this->crawl;
    }

    public function setCrawl(?int $crawl): self
    {
        $this->crawl = $crawl;
        return $this;
    }

    public function getPapillon(): ?int
    {
        return $this->papillon;
    }

    public function setPapillon(?int $papillon): self
    {
        $this->papillon = $papillon;
        return $this;
    }

    public function getDosCrawl(): ?int
    {
        return $this->dosCrawl;
    }

    public function setDosCrawl(?int $dosCrawl): self
    {
        $this->dosCrawl = $dosCrawl;
        return $this;
    }

    public function getBrasse(): ?int
    {
        return $this->brasse;
    }

    public function setBrasse(?int $brasse): self
    {
        $this->brasse = $brasse;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;
        return $this;
    }
}
