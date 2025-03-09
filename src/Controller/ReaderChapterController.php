<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Story;
use App\Repository\ChapterRepository;
use App\Repository\StoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Public controller for reading story chapters - no authentication required
 */
#[Route('/chapters')]
class ReaderChapterController extends AbstractController
{
    /**
     * Read a specific chapter with navigation to previous/next
     */
    #[Route('/{id}/read', name: 'app_chapter_read', methods: ['GET'])]
    public function read(Chapter $chapter, ChapterRepository $chapterRepository): Response
    {
        $story = $chapter->getStory();

        if (!$story->isStatus() && $story->getUserId() !== $this->getUser()?->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour voir ce chapitre');
        }

        $previousChapter = $chapterRepository->findPreviousChapter($chapter);
        $nextChapter = $chapterRepository->findNextChapter($chapter);

        return $this->render('pages/chapters/read.html.twig', [
            'chapter' => $chapter,
            'story' => $story,
            'previousChapter' => $previousChapter,
            'nextChapter' => $nextChapter,
        ]);
    }

    /**
     * Start reading a story from the first chapter
     */
    #[Route('/story/{storyId}/read', name: 'app_story_read', methods: ['GET'])]
    public function readFirstChapter(int $storyId, StoryRepository $storyRepository, ChapterRepository $chapterRepository): Response
    {
        $story = $storyRepository->find($storyId);

        if (!$story) {
            throw $this->createNotFoundException('Histoire non trouvée');
        }

        if (!$story->isStatus() && $story->getUserId() !== $this->getUser()?->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour voir cette histoire');
        }

        $firstChapter = $chapterRepository->findFirstChapter($story);

        if (!$firstChapter) {
            $this->addFlash('warning', 'Cette histoire n\'a pas encore de chapitres.');
            return $this->redirectToRoute('app_story_show', ['id' => $storyId]);
        }

        return $this->redirectToRoute('app_chapter_read', ['id' => $firstChapter->getId()]);
    }
}