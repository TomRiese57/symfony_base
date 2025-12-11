<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // CORRECTION ICI : On ajoute User::class comme 2ème argument
        $user = $this->getReference(UserFixtures::USER_REFERENCE . '0', User::class);

        // Cas 1 : Tâches récentes
        for ($i = 1; $i <= 3; $i++) {
            $task = new Task();
            $task->setName("Tâche récente $i");
            $task->setDescription("Créée hier, donc modifiable.");
            $task->setAuthor($user);
            $task->setCreatedAt(new \DateTimeImmutable('-1 day'));

            $manager->persist($task);
        }

        // Cas 2 : Tâches anciennes
        for ($i = 1; $i <= 3; $i++) {
            $task = new Task();
            $task->setName("Vieille tâche $i");
            $task->setDescription("Créée il y a 10 jours, touche pas !");
            $task->setAuthor($user);
            $task->setCreatedAt(new \DateTimeImmutable('-10 days'));

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}