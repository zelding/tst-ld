<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Index(columns: ['username'], name: "username_idx")]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: "inviter", targetEntity: Invite::class)]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "user_from")]
    /**
     * Invitatin sent
     * @var Collection<Invite>
     */
    private Collection $invites;

    #[ORM\OneToMany(mappedBy: "invitee", targetEntity: Invite::class)]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "user_to")]
    /**
     * invitation recieved
     * @var Collection<Invite>
     */
    private Collection $invited;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AuthToken::class, orphanRemoval: true)]
    private Collection $authTokens;

    public function __construct(?string $username = null, ?string $password = null)
    {
        $this->invites    = new ArrayCollection();
        $this->invited    = new ArrayCollection();
        $this->authTokens = new ArrayCollection();

        if ($username) {
            $this->setUsername($username);
        }

        if ($password) {
            $this->setPassword($password);
        }
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return [
            'id'       => $this->id,
            'username' => $this->username
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /** @return Collection<Invite> */
    public function getInvites(): Collection
    {
        return $this->invites;
    }

    /** @return Collection<Invite> */
    public function getInvited(): Collection
    {
        return $this->invited;
    }

    /**
     * @return Collection<int, AuthToken>
     */
    public function getAuthTokens(): Collection
    {
        return $this->authTokens;
    }

    public function addAuthToken(AuthToken $authToken): static
    {
        if (!$this->authTokens->contains($authToken)) {
            $this->authTokens->add($authToken);
            $authToken->setUser($this);
        }

        return $this;
    }

    public function removeAuthToken(AuthToken $authToken): static
    {
        if ($this->authTokens->removeElement($authToken)) {
            // set the owning side to null (unless already changed)
            if ($authToken->getUser() === $this) {
                $authToken->setUser(null);
            }
        }

        return $this;
    }
}
