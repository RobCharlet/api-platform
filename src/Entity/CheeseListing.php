<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\CheeseListingRepository;
use App\Validator\IsValidOwner;
use App\Validator\ValidIsPublished;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"cheese:read"}},
 *     denormalizationContext={"groups"={"cheese:write"}},
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"cheese:read", "cheese:item:get"}},
 *          },
 *          "put"={
 *              "security" = "is_granted('EDIT', object)",
 *              "security_message" = "only the creator can edit a cheese listing"
 *          },
 *          "delete"={
 *              "security" = "is_granted('ROLE_ADMIN')"
 *          }
 *      },
 *     collectionOperations={
 *          "get",
 *          "post"={
 *              "security" = "is_granted('ROLE_USER')"
 *          }
 *     },
 *     shortName="cheese",
 *     attributes={
 *       "pagination_items_per_page"=10,
 *       "formats"={"jsonld", "json", "html", "jsonhal", "csv"={"text/csv"}}
 *     }
 * )
 * @ApiFilter(BooleanFilter::class, properties={"isPublished"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "title": "partial",
 *     "description": "partial",
 *     "owner": "exact",
 *     "owner.username": "partial"
 * })
 * @ApiFilter(RangeFilter::class, properties={"price"})
 * @ApiFilter(PropertyFilter::class)
 *
 * @ORM\Entity(repositoryClass=CheeseListingRepository::class)
 * @ORM\EntityListeners({"App\Doctrine\CheeseListingSetOwnerListener"})
 * @ValidIsPublished()
 */
class CheeseListing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"cheese:read", "cheese:write", "user:read", "user:write"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     maxMessage="Describe your cheeses with 50 chars or less"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"cheese:read", "user:write"})
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * The price of the cheese in cents
     *
     * @Groups({"cheese:read", "cheese:write", "user:read", "user:write"})
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThan(
     *     value = 0
     * )
     */
    private $price;

    /**
     * @Groups({"cheese:write"})
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Groups({"cheese:write"})
     * @ORM\Column(type="boolean")
     */
    private $isPublished = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cheeseListings")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"cheese:read", "cheese:collection:post"})
     * @IsValidOwner()
     */
    private $owner;

    public function __construct(string $title = null)
    {
        $this->title = $title;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @Groups({"cheese:read"})
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 20) {
            return $this->description;
        }

        $description = strip_tags($this->description);
        return substr($description,0, 20).'...';
    }

    public function setDescription(string $description): self {
        $this->description = $description;

        return $this;
    }

    /**
     * The description of the cheese as raw text.
     *
     * @Groups({"cheese:write"})
     * @SerializedName("description")
     */
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * How long ago in text that this cheese listing was added.
     *
     * @Groups("cheese:read")
     */
    public function getCreatedAtAgo(): string {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    /**
     * How long ago in text this ch
     *
     * @Groups({"cheese:read", "cheese:write"})
     */
    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
