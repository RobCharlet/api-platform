<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\UserSetIsMvpListener;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *
 * @ApiResource(
 *     security="is_granted('ROLE_USER')",
 *     collectionOperations={
 *         "get",
 *         "post" = {
 *              "security" = "is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *              "validation_groups" = { "Default", "create" }
 *          }
 *     },
 *     itemOperations={
 *         "get",
 *         "put" = {"security" = "is_granted('ROLE_USER') and object == user"},
 *         "delete" = {"security" = "is_granted('ROLE_ADMIN')"},
 *     },
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"user:write"}},
 * )
 * @ApiFilter(PropertyFilter::class)
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 * @ORM\EntityListeners({UserSetIsMvpListener::class})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"admin:write"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups({"user:write"})
     * @SerializedName("password")
     * @Assert\NotBlank(groups={"create"})
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user:read", "user:write", "cheese:item:get"})
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity=CheeseListing::class, mappedBy="owner", cascade={"persist"}, orphanRemoval=true)
     * @Groups({"user:write"})
     * @Assert\Valid()
     */
    private $cheeseListings;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"admin:read","owner:read","user:write"})
     */
    private $phoneNumber;

    /**
     * Return true if this is the currently-authenticated
     *
     * @Groups({"user:read"})
     */
    private $isMe = false;

    /**
     * Return true if this is the currently-authenticated
     *
     * @Groups({"user:read"})
     */
    private $isMvp = false;

    public function __construct()
    {
        $this->cheeseListings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|CheeseListing[]
     */
    public function getCheeseListings(): Collection
    {
        return $this->cheeseListings;
    }

    /**
     * @ApiProperty(readableLink=true)
     * @Groups({"user:read"})
     * @SerializedName("cheeseListing")
     * @return Collection<CheeseListing>
     */
    public function getPublishedListings(): Collection
    {
        return $this->cheeseListings->filter(function (CheeseListing $cheeseListing) {
            return $cheeseListing->getIsPublished();
        });
    }

    public function addCheeseListing(CheeseListing $cheeseListing): self
    {
        if (!$this->cheeseListings->contains($cheeseListing)) {
            $this->cheeseListings[] = $cheeseListing;
            $cheeseListing->setOwner($this);
        }

        return $this;
    }

    public function removeCheeseListing(CheeseListing $cheeseListing): self
    {
        if ($this->cheeseListings->contains($cheeseListing)) {
            $this->cheeseListings->removeElement($cheeseListing);
            // set the owning side to null (unless already changed)
            if ($cheeseListing->getOwner() === $this) {
                $cheeseListing->setOwner(null);
            }
        }

        return $this;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getIsMe(): bool
    {
        //if ($this->isMe === null) {
        //  throw new \LogicException('The isMe field has not been initialized');
        //}

        return $this->isMe;
    }

    public function setIsMe(bool $isMe)
    {
        $this->isMe = $isMe;
    }

    public function getIsMvp(): bool
    {
        return $this->isMvp;
    }

    public function setIsMvp(bool $isMvp)
    {
        $this->isMvp = $isMvp;
    }
}
