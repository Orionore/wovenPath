<?php

namespace App\DataFixtures;

use App\Entity\Chapter;
use App\Entity\Story;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChapterFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Chapitres pour la première histoire
        $this->createChapter(
            $manager,
            'story_0',
            null,
            'Vous vous réveillez au milieu d\'une forêt inconnue. Les arbres semblent murmurer autour de vous.',
            0
        );

        $this->createChapter(
            $manager,
            'story_0',
            'chapter_0',
            'Vous suivez un sentier qui s\'enfonce plus profondément dans la forêt.',
            1
        );

        $this->createChapter(
            $manager,
            'story_0',
            'chapter_0',
            'Vous décidez de grimper à un arbre pour avoir une meilleure vue.',
            2
        );

        // Chapitres pour la deuxième histoire
        $this->createChapter(
            $manager,
            'story_1',
            null,
            'Le corps a été découvert à l\'aube. Personne n\'a rien vu, personne n\'a rien entendu.',
            3
        );

        $this->createChapter(
            $manager,
            'story_1',
            'chapter_3',
            'Vous interrogez les voisins qui vous parlent d\'un visiteur nocturne.',
            4
        );

        // Chapitres pour la troisième histoire
        $this->createChapter(
            $manager,
            'story_2',
            null,
            'La machine à voyager dans le temps était prête. Vous appuyez sur le bouton rouge.',
            5
        );

        $manager->flush();
    }

    private function createChapter(ObjectManager $manager, string $storyRef, ?string $parentChapterRef, string $content, int $index): void
    {
        $chapter = new Chapter();

        // Récupérer l'histoire référencée
        $story = $this->getReference($storyRef, Story::class);
        $chapter->setStoryId($story->getId());

        // Définir le chapitre parent s'il existe
        if ($parentChapterRef) {
            $parentChapter = $this->getReference($parentChapterRef, Chapter::class);
            $chapter->setParentChapterId($parentChapter->getId());
        }

        $chapter->setContent($content);
        $chapter->setCreatedAt(new DateTimeImmutable('now'));

        $manager->persist($chapter);

        // Ajouter une référence
        $this->addReference('chapter_'.$index, $chapter);
    }

    public function getDependencies(): array
    {
        return [
            StoryFixtures::class,
        ];
    }
}