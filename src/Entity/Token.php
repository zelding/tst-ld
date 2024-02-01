<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Invite token
 */
#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Index(name: "validaity_idx", columns: ['valid_until'])]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $hash = null;

    #[ORM\Column]
    private int $status = 0;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at  = null;

    #[ORM\Column]
    private ?DateTimeImmutable $valid_until = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "invites")]
    #[ORM\JoinColumn(name: "user_from", referencedColumnName: "id")]
    private User|null $inviter;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "invited")]
    #[ORM\JoinColumn(name: "user_to", referencedColumnName: "id")]
    private User|null $invitee;

    public function __construct() {
        $this->setCreatedAt(new DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getValidUntil(): ?DateTimeImmutable
    {
        return $this->valid_until;
    }

    public function setValidUntil(DateTimeImmutable $valid_until): static
    {
        $this->valid_until = $valid_until;

        return $this;
    }

    public function getInviter(): ?User
    {
        return $this->inviter;
    }

    public function setInviter(?User $inviter): static
    {
        $this->inviter = $inviter;

        return $this;
    }

    public function getInvitee(): ?User
    {
        return $this->invitee;
    }

    public function setInvitee(?User $invitee): static
    {
        $this->invitee = $invitee;

        return $this;
    }

}
