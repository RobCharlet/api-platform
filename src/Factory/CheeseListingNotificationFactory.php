<?php

namespace App\Factory;

use App\Entity\CheeseListing;
use App\Entity\CheeseListingNotification;
use App\Repository\CheeseListingNotificationRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static CheeseListingNotification|Proxy findOrCreate(array $attributes)
 * @method static CheeseListingNotification|Proxy random()
 * @method static CheeseListingNotification[]|Proxy[] randomSet(int $number)
 * @method static CheeseListingNotification[]|Proxy[] randomRange(int $min, int $max)
 * @method static CheeseListingNotificationRepository|RepositoryProxy repository()
 * @method CheeseListingNotification|Proxy create($attributes = [])
 * @method CheeseListingNotification[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class CheeseListingNotificationFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'cheeseListing' => CheeseListingFactory::new(),
            'notificationText' => self::faker()->realText(50),
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(CheeseListingNotification $cheeseListingNotification) {})
        ;
    }

    protected static function getClass(): string
    {
        return CheeseListingNotification::class;
    }
}
