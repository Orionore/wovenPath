<?php

namespace App\DataFixtures;

use App\Entity\Story;
use App\Entity\User;
use App\Enum\StoryEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $stories = [
            [
                'title' => 'Le mystère de la forêt enchantée',
                'description' => 'Une aventure mystérieuse dans une forêt pleine de secrets',
                'status' => true,
                'user' => 'user_0', // Référence à l'utilisateur admin
                'genre' => [StoryEnum::FANTASY, StoryEnum::ADVENTURE]
            ],
            [
                'title' => 'L\'enquête impossible',
                'description' => 'Un détective face à son affaire la plus complexe',
                'status' => true,
                'user' => 'user_1', // Référence à l'utilisateur user1
                'genre' => [StoryEnum::MYSTERY, StoryEnum::THRILLER]
            ],
            [
                'title' => 'Voyage dans le temps',
                'description' => 'Une expérience temporelle qui tourne mal',
                'status' => false,
                'user' => 'user_2', // Référence à l'utilisateur user2
                'genre' => [StoryEnum::SCIENCE_FICTION]
            ],
        ];

        foreach ($stories as $key => $storyData) {
            $story = new Story();
            $story->setTitle($storyData['title']);
            $story->setDescription($storyData['description']);
            $story->setStatus($storyData['status']);

            // Récupérer l'utilisateur référencé
            $user = $this->getReference($storyData['user'], User::class);
            $story->setUserId($user->getId());

            $story->setGenre($storyData['genre']);
            $story->prePersist();

            $manager->persist($story);

            // Définir une référence pour pouvoir l'utiliser dans d'autres fixtures
            $this->addReference('story_'.$key, $story);
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