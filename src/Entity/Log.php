<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Fontainebleau;
use App\Repository\LogRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'logs')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'logs')]
    private Route $route;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isMirrored = false;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\PositiveOrZero]
    private ?int $angle = null;

    #[ORM\Column(nullable: true, enumType: Fontainebleau::class)]
    private ?Fontainebleau $grade = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Positive]
    private ?int $rating = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Positive]
    private int $attempts = 1;

    #[ORM\Column(nullable: true, type: Types::TEXT)]
    private ?string $note = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function setRoute(Route $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function isMirrored(): bool
    {
        return $this->isMirrored;
    }

    public function setIsMirrored(bool $isMirrored): static
    {
        $this->isMirrored = $isMirrored;

        return $this;
    }

    public function getAngle(): ?int
    {
        return $this->angle;
    }

    public function setAngle(?int $angle): static
    {
        $this->angle = $angle;

        return $this;
    }

    public function getGrade(): ?Fontainebleau
    {
        return $this->grade;
    }

    public function setGrade(?Fontainebleau $grade): static
    {
        $this->grade = $grade;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): static
    {
        $this->attempts = $attempts;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function initCreatedAtRefrshUpdatedAt(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new DateTimeImmutable();
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'route' => $this->route,
            'is_mirrored' => $this->isMirrored,
            'angle' => $this->angle,
            'grade' => $this->grade,
            'rating' => $this->rating,
            'attempts' => $this->attempts,
            'note' => $this->note,
            'create_at' => $this->createdAt,
        ];
    }
}