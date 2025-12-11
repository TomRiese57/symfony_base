<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // On ne met rien ici car toutes les entités sont gérées dans des fixtures séparées
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TaskFixtures::class,
        ];
    }
}