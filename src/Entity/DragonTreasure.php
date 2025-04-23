<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DragonTreasureRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DragonTreasureRepository::class)]
#[ApiResource(
    shortName: 'Treasure',
    description: 'A treasure guarded by a dragon, with a name, description, value, and cool factor.',
    operations: [
        new \ApiPlatform\Metadata\GetCollection(),
        new \ApiPlatform\Metadata\Get(),
        new \ApiPlatform\Metadata\Post(),
        new \ApiPlatform\Metadata\Put(),
        new \ApiPlatform\Metadata\Patch(),
    ],
)]
class DragonTreasure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * The name of the treasure, which should be unique and descriptive.
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * A detailed description of the treasure, including its history and significance.
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * The estimated value of the treasure in gold coins.
     */
    #[ORM\Column]
    private ?int $value = null;

    /**
     * A subjective measure of how cool the treasure is, on a scale from 1 to 10.
     */
    #[ORM\Column]
    private ?int $coolFactor = null;

    /**
     * The date and time when the treasure was plunder.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $plunderedAt;

    /**
     * Indicates whether the treasure is currently published and available for viewing.
     */
    #[ORM\Column]
    private ?bool $isPublished = null;

    public function __construct()
    {
        $this->plunderedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setTextDescription(string $description): static
    {
        $this->description = nl2br($description);

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getCoolFactor(): ?int
    {
        return $this->coolFactor;
    }

    public function setCoolFactor(int $coolFactor): static
    {
        $this->coolFactor = $coolFactor;

        return $this;
    }

    public function getPlunderedAt(): ?\DateTimeImmutable
    {
        return $this->plunderedAt;
    }

    /**
     * A human-readable representation of the time since the treasure was plundered.
     */
    public function getPlunderedAtAgo(): string    
    {
        return Carbon::instance($this->plunderedAt)->diffForHumans();
    }

    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }
}
