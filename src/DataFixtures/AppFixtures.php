<?php

namespace App\DataFixtures;

use App\Factory\CheeseListingFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        CheeseListingFactory::new()->createMany();
        UserFactory::new()->createMany();

        $manager->flush();
    }
}
