<?php

namespace App\Entity;

use App\Enum\Fontainebleau;
use App\Repository\RouteRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: RouteRepository::class)]
class Route implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** @var array<int, array<int, int>> */
    #[ORM\Column(type: Types::JSON)]
    private array $holdSetup = [];

    #[ORM\Column(length: 190, unique: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true, enumType: Fontainebleau::class)]
    private ?Fontainebleau $grade = null;

    #[ORM\Column(nullable: true, type: Types::TEXT)]
    private ?string $note = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'routes')]
    private User $routeSetter;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return array<int, array<int, int>>
     */
    public function getHoldSetup(): array
    {
        return $this->holdSetup;
    }

    /**
     * @param array<int, array<int, int>> $holdSetup
     */
    public function setHoldSetup(array $holdSetup): static
    {
        $this->holdSetup = $holdSetup;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getRouteSetter(): ?User
    {
        return $this->routeSetter;
    }

    public function setRouteSetter(User $routeSetter): static
    {
        $this->routeSetter = $routeSetter;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initCreatedAtRefrshUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable();
        if ($this->createdAt === null) {
            $this->createdAt = new DateTimeImmutable();
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'hold_setup' => $this->holdSetup,
            'name' => $this->name,
            'grade' => $this->grade,
            'note' => $this->note,
            'route_setter' => $this->routeSetter,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
