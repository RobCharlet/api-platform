<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Action\NotFoundAction;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"daily-stats:read"}},
 *     itemOperations={
 *      "get"={
 *          "method"="GET",
 *          "controller"=NotFoundAction::class,
 *          "read"=false,
 *          "output"=false
 *      },
 *     },
 *     collectionOperations={"get"}
 * )
 */
class DailyStats
{
    /**
     * @Groups({"daily-stats:read"})
     */
    public $date;

    /**
     * @Groups({"daily-stats:read"})
     */
    public $totalVisitors;

    /**
     * The 5 most popular cheese listings from this date!
     * @var array<CheeseListing>|CheeseListing[]
     * @Groups({"daily-stats:read"})
     */
    public $mostPopularListings;

    // ApiPlatform need constructor to guess field type in documentation
    // and deserialization. @var array<CheeseListing>|CheeseListing[] is also
    // needed to get an array of CheeseListing iri and not an array of objects
    // in serialization (|CheeseListing[] is for phpstorm autocompletion).
    public function __construct(
        \DateTime $date,
        int $totalVisitors,
        array $mostPopularListings
    )
    {
        $this->date                = $date;
        $this->totalVisitors       = $totalVisitors;
        $this->mostPopularListings = $mostPopularListings;
    }

    /**
     * @ApiProperty(identifier=true)
     */
    public function getDateString(): string
    {
        return $this->date->format('Y-m-d');
    }


}