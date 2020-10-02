<?php

namespace App\Factory;

use App\Entity\CheeseListing;
use App\Repository\CheeseListingRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static CheeseListing|Proxy findOrCreate(array $attributes)
 * @method static CheeseListing|Proxy random()
 * @method static CheeseListing[]|Proxy[] randomSet(int $number)
 * @method static CheeseListing[]|Proxy[] randomRange(int $min, int $max)
 * @method static CheeseListingRepository|RepositoryProxy repository()
 * @method CheeseListing|Proxy create($attributes = [])
 * @method CheeseListing[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class CheeseListingFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->beforeInstantiate(function(CheeseListing $cheeseListing) {})
        ;
    }

    protected static function getClass(): string
    {
        return CheeseListing::class;
    }
}
