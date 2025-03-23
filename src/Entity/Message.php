<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "message")]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_message", type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Swimmer::class)]
    #[ORM\JoinColumn(name: "sender_id", referencedColumnName: "id_swimmer")]
    private ?Swimmer $sender = null;

    #[ORM\ManyToOne(targetEntity: Swimmer::class)]
    #[ORM\JoinColumn(name: "receiver_id", referencedColumnName: "id_swimmer")]
    private ?Swimmer $receiver = null;

    #[ORM\Column(name: "content", type: "text")]
    private ?string $content = null;

    #[ORM\Column(name: "created_at", type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: "subject", type: "string", length: 255, nullable: true)]
    private ?string $subject = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?Swimmer
    {
        return $this->sender;
    }

    public function setSender(?Swimmer $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getReceiver(): ?Swimmer
    {
        return $this->receiver;
    }

    public function setReceiver(?Swimmer $receiver): self
    {
        $this->receiver = $receiver;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }
}
