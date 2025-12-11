<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user_';

    // On déclare la variable pour le hasher
    private UserPasswordHasherInterface $passwordHasher;

    // On injecte le hasher dans le constructeur
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@example.com');
            $user->setRoles(['ROLE_USER']);

            // Hachage du mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, "123");
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE . $i, $user);
        }

        // Pour l'admin (attention, $i vaut 10 ici car il sort de la boucle)
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($admin, "123");
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);
        $this->addReference(self::USER_REFERENCE . $i, $admin);

        $manager->flush();
    }
}