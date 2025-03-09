<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Story;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for managing chapters by story authors
 */
#[Route('/author')]
#[IsGranted('ROLE_USER')]
class AuthorChapterController extends AbstractController
{
    /**
     * List all chapters of a story with management options
     */
    #[Route('/stories/{id}/chapters', name: 'app_author_story_chapters', methods: ['GET'])]
    public function list(Story $story, ChapterRepository $chapterRepository): Response
    {
        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour gérer les chapitres de cette histoire');
        }

        $chapters = $chapterRepository->findByStory($story);

        return $this->render('pages/chapters/list.html.twig', [
            'story' => $story,
            'chapters' => $chapters,
        ]);
    }

    /**
     * Create a new chapter for a story
     */
    #[Route('/stories/{id}/chapters/new', name: 'app_author_chapter_new', methods: ['GET', 'POST'])]
    public function new(Story $story, Request $request, EntityManagerInterface $entityManager, ChapterRepository $chapterRepository): Response
    {
        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas ajouter de chapitres à cette histoire');
        }

        $chapter = new Chapter();
        $chapter->setStory($story);

        // Set position to be after the last chapter
        $position = $chapterRepository->getMaxPosition($story) + 1;
        $chapter->setPosition($position);

        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chapter);
            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été ajouté avec succès.');
            return $this->redirectToRoute('app_author_story_chapters', ['id' => $story->getId()]);
        }

        return $this->render('pages/chapters/new.html.twig', [
            'story' => $story,
            'form' => $form,
        ]);
    }

    /**
     * Edit an existing chapter
     */
    #[Route('/chapters/{id}/edit', name: 'app_author_chapter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        $story = $chapter->getStory();

        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce chapitre');
        }

        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été modifié avec succès.');
            return $this->redirectToRoute('app_author_story_chapters', ['id' => $story->getId()]);
        }

        return $this->render('pages/chapters/edit.html.twig', [
            'chapter' => $chapter,
            'story' => $story,
            'form' => $form,
        ]);
    }

    /**
     * Delete a chapter
     */
    #[Route('/chapters/{id}/delete', name: 'app_author_chapter_delete', methods: ['POST'])]
    public function delete(Request $request, Chapter $chapter, EntityManagerInterface $entityManager): Response
    {
        $story = $chapter->getStory();

        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce chapitre');
        }

        if ($this->isCsrfTokenValid('delete'.$chapter->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chapter);
            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_author_story_chapters', ['id' => $story->getId()]);
    }

    /**
     * Change the position of a chapter (up/down)
     */
    #[Route('/chapters/{id}/move/{direction}', name: 'app_author_chapter_move', methods: ['POST'])]
    public function move(Chapter $chapter, string $direction, EntityManagerInterface $entityManager, ChapterRepository $chapterRepository): Response
    {
        $story = $chapter->getStory();

        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas réorganiser ce chapitre');
        }

        $currentPosition = $chapter->getPosition();

        if ($direction === 'up' && $currentPosition > 1) {
            $previousChapter = $chapterRepository->findPreviousChapter($chapter);

            if ($previousChapter) {
                $previousPosition = $previousChapter->getPosition();

                $previousChapter->setPosition($currentPosition);
                $chapter->setPosition($previousPosition);

                $entityManager->flush();
                $this->addFlash('success', 'Le chapitre a été déplacé vers le haut.');
            }
        } elseif ($direction === 'down') {
            $nextChapter = $chapterRepository->findNextChapter($chapter);

            if ($nextChapter) {
                $nextPosition = $nextChapter->getPosition();

                $nextChapter->setPosition($currentPosition);
                $chapter->setPosition($nextPosition);

                $entityManager->flush();
                $this->addFlash('success', 'Le chapitre a été déplacé vers le bas.');
            }
        }

        return $this->redirectToRoute('app_author_story_chapters', ['id' => $story->getId()]);
    }
}