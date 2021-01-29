<?php

namespace App\Dto;

use App\Entity\User;
use Carbon\Carbon;
use Symfony\Component\Serializer\Annotation\Groups;

class CheeseListingOutput
{
    /**
     * @Groups({"cheese:read", "user:read"})
     * @var string $title
     */
    public $title;

    /**
     * @Groups({"cheese:read"})
     * @var string $description
     */
    public $description;

    /**
     * @Groups({"cheese:read", "user:read"})
     * @var int $price
     */
    public $price;

    public $createdAt;

    /**
     * @Groups({"cheese:read"})
     * @var User
     */
    public $owner;

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

    /**
     * How long ago in text that this cheese listing was added.
     *
     * @Groups("cheese:read")
     */
    public function getCreatedAtAgo(): string {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }
}