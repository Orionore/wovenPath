<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Story;
use App\Entity\Chapter;
use App\Entity\Choice;
use App\Entity\ReadinProgress;
use App\Enum\StoryEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    // Variable pour stocker les références
    private array $users = [];
    private array $stories = [];
    private array $chapters = [];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadStories($manager);
        $this->loadChapters($manager);
        $this->loadChoices($manager);
        $this->loadReadingProgress($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        // Créer des utilisateurs de test
        $userDataList = [
            [
                'email' => 'admin@example.com',
                'username' => 'admin',
                'roles' => ['ROLE_ADMIN'],
                'password' => 'admin123',
            ],
            [
                'email' => 'author@example.com',
                'username' => 'author',
                'roles' => ['ROLE_AUTHOR'],
                'password' => 'author123',
            ],
            [
                'email' => 'reader@example.com',
                'username' => 'reader',
                'roles' => ['ROLE_USER'],
                'password' => 'reader123',
            ],
            [
                'email' => 'john.doe@example.com',
                'username' => 'JohnDoe',
                'roles' => ['ROLE_USER'],
                'password' => 'password123',
            ],
            [
                'email' => 'jane.doe@example.com',
                'username' => 'JaneDoe',
                'roles' => ['ROLE_AUTHOR'],
                'password' => 'password123',
            ],
        ];

        foreach ($userDataList as $index => $userData) {
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

            $user->setCreatedAt(new DateTimeImmutable());

            $manager->persist($user);
            $this->users[$index] = $user;
        }

        $manager->flush();
    }

    private function loadStories(ObjectManager $manager): void
    {
        $storiesData = [
            [
                'user_index' => 0, // admin
                'title' => 'L\'Aventure Mystérieuse',
                'description' => 'Une histoire d\'aventure où chaque choix détermine le destin du protagoniste.',
                'status' => true,
                'genre' => [StoryEnum::AVENTURE, StoryEnum::FANTASTIQUE],
            ],
            [
                'user_index' => 0, // admin
                'title' => 'Enquête dans la Nuit',
                'description' => 'Un thriller passionnant où vous incarnez un détective résolvant un mystère complexe.',
                'status' => true,
                'genre' => [StoryEnum::POLICIER, StoryEnum::THRILLER],
            ],
            [
                'user_index' => 1, // author
                'title' => 'La Quête Légendaire',
                'description' => 'Embarquez dans un monde fantastique à la recherche d\'artefacts anciens.',
                'status' => true,
                'genre' => [StoryEnum::FANTASY, StoryEnum::AVENTURE],
            ],
            [
                'user_index' => 3, // JohnDoe
                'title' => 'Amour à Paris',
                'description' => 'Une histoire d\'amour dans la ville de la lumière, pleine de choix et de conséquences.',
                'status' => true,
                'genre' => [StoryEnum::ROMANCE, StoryEnum::DRAME],
            ],
            [
                'user_index' => 3, // JohnDoe
                'title' => 'Étoiles Perdues',
                'description' => 'Une aventure spatiale épique avec des rencontres extraterrestres et des dilemmes moraux.',
                'status' => false, // histoire en brouillon
                'genre' => [StoryEnum::SCIENCE_FICTION, StoryEnum::AVENTURE],
            ],
        ];

        foreach ($storiesData as $index => $storyData) {
            $story = new Story();
            $story->setUser($this->users[$storyData['user_index']]);
            $story->setTitle($storyData['title']);
            $story->setDescription($storyData['description']);
            $story->setStatus($storyData['status']);
            $story->setGenre($storyData['genre']);
            $story->setCreatedAt(new DateTimeImmutable());

            $manager->persist($story);
            $this->stories[$index] = $story;
        }

        $manager->flush();
    }

    private function loadChapters(ObjectManager $manager): void
    {
        // Histoire 1 : L'Aventure Mystérieuse
        $story1ChaptersData = [
            [
                'story_index' => 0,
                'parent_chapter_id' => null, // chapitre initial
                'content' => "Vous vous réveillez dans une forêt dense. Vous ne vous souvenez pas comment vous êtes arrivé ici. Devant vous se trouvent deux chemins : l'un mène vers une montagne escarpée, l'autre vers ce qui semble être une rivière.",
            ],
            [
                'story_index' => 0,
                'parent_chapter_id' => 0, // issu du premier chapitre
                'content' => "Vous décidez de prendre le chemin vers la montagne. Après une heure de marche difficile, vous atteignez une grotte étrange. À l'intérieur, vous voyez des inscriptions anciennes sur les murs.",
            ],
            [
                'story_index' => 0,
                'parent_chapter_id' => 0, // issu du premier chapitre
                'content' => "Vous suivez le chemin vers la rivière. En vous approchant, vous découvrez un petit village de pêcheurs. Les habitants vous regardent avec curiosité.",
            ],
        ];

        // Histoire 2 : Enquête dans la Nuit
        $story2ChaptersData = [
            [
                'story_index' => 1,
                'parent_chapter_id' => null, // chapitre initial
                'content' => "Le corps a été découvert à 23h dans l'appartement 3B. En tant que détective en chef, vous êtes le premier sur la scène de crime. L'inspecteur Johnson vous attend à l'entrée. Allez-vous d'abord interroger les voisins ou examiner la scène de crime ?",
            ],
            [
                'story_index' => 1,
                'parent_chapter_id' => 3, // issu du premier chapitre de l'histoire 2
                'content' => "Vous décidez d'examiner la scène de crime avant que les preuves ne soient altérées. La victime est allongée près de la fenêtre ouverte. Vous remarquez des traces de lutte et un objet brillant sous le canapé.",
            ],
            [
                'story_index' => 1,
                'parent_chapter_id' => 3, // issu du premier chapitre de l'histoire 2
                'content' => "Vous choisissez d'interroger d'abord les voisins. La voisine de l'appartement 3A, une vieille dame, vous dit avoir entendu une dispute vers 22h, suivie d'un bruit sourd.",
            ],
        ];

        // Combiner tous les chapitres
        $allChaptersData = array_merge($story1ChaptersData, $story2ChaptersData);

        // Premier passage pour créer tous les chapitres
        foreach ($allChaptersData as $index => $chapterData) {
            $chapter = new Chapter();
            $chapter->setStory($this->stories[$chapterData['story_index']]);
            // On définira le parent dans un second passage
            $chapter->setContent($chapterData['content']);
            $chapter->setCreatedAt(new DateTimeImmutable());

            $manager->persist($chapter);
            $this->chapters[$index] = $chapter;
        }

        $manager->flush();

        // Second passage pour définir les relations de parenté entre chapitres
        foreach ($allChaptersData as $index => $chapterData) {
            if ($chapterData['parent_chapter_id'] !== null) {
                $this->chapters[$index]->setParentChapter($this->chapters[$chapterData['parent_chapter_id']]);
                $manager->persist($this->chapters[$index]);
            }
        }

        $manager->flush();
    }

    private function loadChoices(ObjectManager $manager): void
    {
        $choicesData = [
            // Choix pour le premier chapitre de l'histoire 1
            [
                'chapter_index' => 0, // chapitre initial de l'histoire 1
                'next_chapter_index' => 1, // va vers le chapitre de la montagne
                'choice_text' => 'Prendre le chemin de la montagne',
                'created_by' => 'admin',
                'approved' => true,
            ],
            [
                'chapter_index' => 0, // chapitre initial de l'histoire 1
                'next_chapter_index' => 2, // va vers le chapitre de la rivière
                'choice_text' => 'Suivre le chemin vers la rivière',
                'created_by' => 'admin',
                'approved' => true,
            ],

            // Choix pour le premier chapitre de l'histoire 2
            [
                'chapter_index' => 3, // chapitre initial de l'histoire 2
                'next_chapter_index' => 4, // va vers l'examen de la scène de crime
                'choice_text' => 'Examiner la scène de crime',
                'created_by' => 'author',
                'approved' => true,
            ],
            [
                'chapter_index' => 3, // chapitre initial de l'histoire 2
                'next_chapter_index' => 5, // va vers l'interrogation des voisins
                'choice_text' => 'Interroger les voisins',
                'created_by' => 'author',
                'approved' => true,
            ],

            // Choix supplémentaires (pas encore approuvés)
            [
                'chapter_index' => 1, // chapitre de la montagne
                'next_chapter_index' => null, // pas encore de chapitre suivant créé
                'choice_text' => 'Explorer plus profondément la grotte',
                'created_by' => 'reader',
                'approved' => false,
            ],
        ];

        foreach ($choicesData as $choiceData) {
            $choice = new Choice();
            $choice->setChapter($this->chapters[$choiceData['chapter_index']]);

            if ($choiceData['next_chapter_index'] !== null) {
                $choice->setNextChapter($this->chapters[$choiceData['next_chapter_index']]);
            }

            $choice->setChoiceText($choiceData['choice_text']);
            $choice->setCreatedBy($choiceData['created_by']);
            $choice->setApproved($choiceData['approved']);

            $manager->persist($choice);
        }

        $manager->flush();
    }

    private function loadReadingProgress(ObjectManager $manager): void
    {
        $readingProgressData = [
            [
                'user_index' => 2, // reader
                'story_index' => 0, // L'Aventure Mystérieuse
                'current_chapter_index' => 1, // Le chapitre de la montagne
                'path' => [1, 2], // A commencé par le chapitre 1, puis est allé au 2
            ],
            [
                'user_index' => 2, // reader
                'story_index' => 1, // Enquête dans la Nuit
                'current_chapter_index' => 3, // Encore au premier chapitre
                'path' => [4], // Seulement le chapitre initial
            ],
            [
                'user_index' => 3, // JohnDoe
                'story_index' => 0, // L'Aventure Mystérieuse
                'current_chapter_index' => 2, // Le chapitre de la rivière
                'path' => [1, 3], // A commencé par le chapitre 1, puis est allé au 3
            ],
        ];

        foreach ($readingProgressData as $progressData) {
            $progress = new ReadinProgress();
            $progress->setUser($this->users[$progressData['user_index']]);
            $progress->setStory($this->stories[$progressData['story_index']]);
            $progress->setCurrentChapter($this->chapters[$progressData['current_chapter_index']]);
            $progress->setPath($progressData['path']);
            $progress->setLastReadAt(new DateTimeImmutable());

            $manager->persist($progress);
        }

        $manager->flush();
    }
}