<?php

namespace App\DataFixtures;

use App\Entity\Chapter;
use App\Entity\ReadinProgress;
use App\Entity\Story;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReadingProgressFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // L'utilisateur 1 lit l'histoire 1 et est au chapitre 1
        $this->createReadingProgress(
            $manager,
            'user_1',
            'story_0',
            'chapter_1',
            [1] // Chemin simple de lecture
        );

        // L'utilisateur 2 lit l'histoire 2 et est au chapitre 4
        $this->createReadingProgress(
            $manager,
            'user_2',
            'story_1',
            'chapter_4',
            [3, 4] // Chemin de lecture: a commencé par le chapitre 3 puis est allé au 4
        );

        // L'admin lit l'histoire 3
        $this->createReadingProgress(
            $manager,
            'user_0',
            'story_2',
            'chapter_5',
            [5]
        );

        $manager->flush();
    }

    private function createReadingProgress(
        ObjectManager $manager,
        string $userRef,
        string $storyRef,
        string $currentChapterRef,
        array $path
    ): void {
        $readingProgress = new ReadinProgress();

        // Récupérer les références
        $user = $this->getReference($userRef, User::class);
        $story = $this->getReference($storyRef, Story::class);
        $currentChapter = $this->getReference($currentChapterRef, Chapter::class);

        $readingProgress->setUserId($user->getId());
        $readingProgress->setStoryId($story->getId());
        $readingProgress->setCurrentChapterId($currentChapter->getId());
        $readingProgress->setPath($path);
        $readingProgress->setLastReadAt(new DateTimeImmutable('now'));

        $manager->persist($readingProgress);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            StoryFixtures::class,
            ChapterFixtures::class,
        ];
    }
}