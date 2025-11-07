<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Setting::class)]
    private ?Setting $parent = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Positive]
    private ?int $rowCount = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Positive]
    private ?int $columnCount = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isSymmetric = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isAdjustable = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getParent(): ?Setting
    {
        return $this->parent;
    }

    public function setParent(Setting $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getRowCount(): ?int
    {
        return $this->rowCount;
    }

    public function setRowCount(int $rowCount): static
    {
        $this->rowCount = $rowCount;

        return $this;
    }

    public function getColumnCount(): ?int
    {
        return $this->columnCount;
    }

    public function setColumnCount(int $columnCount): static
    {
        $this->columnCount = $columnCount;

        return $this;
    }

    public function isSymmetric(): bool
    {
        return $this->isSymmetric;
    }

    public function setIsSymmetric(bool $isSymmetric): static
    {
        $this->isSymmetric = $isSymmetric;

        return $this;
    }

    public function isAdjustable(): bool
    {
        return $this->isAdjustable;
    }

    public function setIsAdjustable(bool $isAdjustable): static
    {
        $this->isAdjustable = $isAdjustable;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function initCreatedAt(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new DateTimeImmutable();
        }
    }
}
