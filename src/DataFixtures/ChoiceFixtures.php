<?php

namespace App\DataFixtures;

use App\Entity\Chapter;
use App\Entity\Choice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChoiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Choix pour le premier chapitre de la première histoire
        $this->createChoice(
            $manager,
            'chapter_0',
            'chapter_1',
            'Suivre le sentier',
            'admin',
            true
        );

        $this->createChoice(
            $manager,
            'chapter_0',
            'chapter_2',
            'Grimper à un arbre',
            'admin',
            true
        );

        // Choix pour le second chapitre
        $this->createChoice(
            $manager,
            'chapter_1',
            null, // Pas encore de chapitre suivant
            'Continuer à avancer prudemment',
            'user1',
            false
        );

        // Choix pour le premier chapitre de la deuxième histoire
        $this->createChoice(
            $manager,
            'chapter_3',
            'chapter_4',
            'Interroger les voisins',
            'user1',
            true
        );

        $this->createChoice(
            $manager,
            'chapter_3',
            null,
            'Examiner la scène de crime plus en détail',
            'user2',
            false
        );

        $manager->flush();
    }

    private function createChoice(
        ObjectManager $manager,
        string $chapterRef,
        ?string $nextChapterRef,
        string $choiceText,
        string $createdBy,
        bool $approved
    ): void {
        $choice = new Choice();

        // Récupérer le chapitre référencé
        $chapter = $this->getReference($chapterRef, Chapter::class);
        $choice->setChapterId($chapter->getId());

        // Définir le chapitre suivant s'il existe
        if ($nextChapterRef) {
            $nextChapter = $this->getReference($nextChapterRef, Chapter::class);
            $choice->setNextChapterId($nextChapter->getId());
        }

        $choice->setChoiceText($choiceText);
        $choice->setCreatedBy($createdBy);
        $choice->setApproved($approved);

        $manager->persist($choice);
    }

    public function getDependencies(): array
    {
        return [
            ChapterFixtures::class,
        ];
    }
}