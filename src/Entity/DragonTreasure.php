<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\DragonTreasureRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: DragonTreasureRepository::class)]
#[ORM\Table(name: '`dragon_treasure`')]
#[ApiResource(
    shortName: 'Treasure',
    description: 'A treasure guarded by a dragon, with a name, description, value, and cool factor.',
    operations: [
        new \ApiPlatform\Metadata\GetCollection(),
        new \ApiPlatform\Metadata\Get(),
        new \ApiPlatform\Metadata\Post(),
        new \ApiPlatform\Metadata\Put(),
        new \ApiPlatform\Metadata\Patch(),
        new \ApiPlatform\Metadata\Delete(),
    ],
    normalizationContext: [
        'groups' => ['treasure:read'],
    ],
    denormalizationContext: [
        'groups' => ['treasure:write'],
    ],
    paginationItemsPerPage: 10,
    formats: [
        'jsonld',
        'json',
        'html',
        'csv' => 'text/csv',
    ],
)]
/* Subresource for the User entity
 * This allows us to fetch all treasures owned by a specific user
 * using the URI /users/{user_id}/treasures
 */
#[ApiResource(
    shortName: 'Treasure',
    operations: [
        new GetCollection(
            uriTemplate: '/users/{user_id}/treasures.{_format}',
            uriVariables: [
                'user_id' => new Link(
                    fromClass: User::class,
                    fromProperty: 'dragonTreasures'
                ),
            ],
            normalizationContext: ['groups' => ['treasure:read']],
        ),
    ],
)]
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(SearchFilter::class, properties: [
    'owner.username' => 'partial',
])]
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
    #[Groups(['treasure:read', 'treasure:write', 'user:read', 'user:write'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50, maxMessage: 'Describe your loot in 50 chars or less')]
    private ?string $name = null;

    /**
     * A detailed description of the treasure, including its history and significance.
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['treasure:read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    #[Assert\NotBlank]
    private ?string $description = null;

    /**
     * The estimated value of the treasure in gold coins.
     */
    #[ORM\Column]
    #[Groups(['treasure:read', 'treasure:write', 'user:read', 'user:write'])]
    #[ApiFilter(RangeFilter::class)]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $value = 0;

    /**
     * A subjective measure of how cool the treasure is, on a scale from 1 to 10.
     */
    #[ORM\Column]
    #[Groups(['treasure:read', 'treasure:write'])]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\LessThanOrEqual(10)]
    private ?int $coolFactor = 0;

    /**
     * The date and time when the treasure was plunder.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $plunderedAt;

    /**
     * Indicates whether the treasure is currently published and available for viewing.
     */
    #[ORM\Column]
    #[ApiFilter(BooleanFilter::class)]
    private ?bool $isPublished = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'dragonTreasures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['treasure:read', 'treasure:write'])]
    #[Assert\Valid]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    private ?User $owner = null;

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

    #[Groups(['treasure:read'])]
    public function getShortDescription(): string
    {
        return u($this->getDescription())->truncate(40, '...');
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    #[SerializedName('description')]
    #[Groups(['treasure:write', 'user:write'])]
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

    public function setPlunderedAt($plunderedAt): static
    {
        $this->plunderedAt = $plunderedAt;

        return $this;
    }

    public function getPlunderedAt(): ?\DateTimeImmutable
    {
        return $this->plunderedAt;
    }

    /**
     * A human-readable representation of the time since the treasure was plundered.
     */
    #[Groups(['treasure:read'])]
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
