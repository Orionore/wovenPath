<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créons quelques utilisateurs
        $users = [
            [
                'email' => 'admin@example.com',
                'username' => 'admin',
                'roles' => ['ROLE_ADMIN'],
                'password' => 'admin123'
            ],
            [
                'email' => 'user1@example.com',
                'username' => 'user1',
                'roles' => ['ROLE_USER'],
                'password' => 'user123'
            ],
            [
                'email' => 'user2@example.com',
                'username' => 'user2',
                'roles' => ['ROLE_USER'],
                'password' => 'user123'
            ],
        ];

        foreach ($users as $key => $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setUsername($userData['username']);
            $user->setRoles($userData['roles']);

            // Hasher le mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $userData['password']
            );
            $user->setPassword($hashedPassword);

            $user->setCreatedAt(new DateTimeImmutable('now'));

            $manager->persist($user);

            // Définir une référence pour pouvoir l'utiliser dans d'autres fixtures
            $this->addReference('user_'.$key, $user);
        }

        $manager->flush();
    }
}