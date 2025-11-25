<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Route::class, mappedBy: 'routeSetter')]
    private Collection $routes;

    #[ORM\OneToMany(targetEntity: Log::class, mappedBy: 'user')]
    private Collection $logs;

    #[ORM\ManyToMany(targetEntity: Route::class)]
    private Collection $bookmarks;

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

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoutes(): Collection
    {
        return $this->routes;
    }

    public function setRoutes(Collection $routes): static
    {
        $this->routes = $routes;

        return $this;
    }

    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function setLogs(Collection $logs): static
    {
        $this->logs = $logs;

        return $this;
    }

    public function getBookmarks(): Collection
    {
        return $this->bookmarks;
    }

    public function setBookmarks(Collection $bookmarks): static
    {
        $this->bookmarks = $bookmarks;

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'user_identifier' => $this->username,
            'routes' => $this->routes,
            'logs' => $this->logs,
        ];
    }
}
