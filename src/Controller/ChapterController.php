<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Story;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use App\Repository\StoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/chapter')]
class ChapterController extends AbstractController
{
    #[Route('/story/{id}/chapters', name: 'app_story_chapters', methods: ['GET'])]
    public function list(Story $story, ChapterRepository $chapterRepository): Response
    {
        if (!$story->isStatus() && $story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour voir cette histoire');
        }

        $chapters = $chapterRepository->findByStory($story);

        return $this->render('pages/chapter/list.html.twig', [
            'story' => $story,
            'chapters' => $chapters,
        ]);
    }

    #[Route('/story/{id}/chapters/new', name: 'app_chapter_new', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Story $story, Request $request, EntityManagerInterface $entityManager, ChapterRepository $chapterRepository): Response
    {
        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas ajouter de chapitres à cette histoire');
        }

        $chapter = new Chapter();
        $chapter->setStory($story);

        $position = $chapterRepository->getMaxPosition($story) + 1;
        $chapter->setPosition($position);

        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chapter);
            $entityManager->flush();

            $this->addFlash('success', 'Le chapitre a été ajouté avec succès.');
            return $this->redirectToRoute('app_story_chapters', ['id' => $story->getId()]);
        }

        return $this->render('pages/chapter/new.html.twig', [
            'story' => $story,
            'form' => $form,
        ]);
    }

    #[Route('/read/{id}', name: 'app_chapter_read', methods: ['GET'])]
    public function read(Chapter $chapter, ChapterRepository $chapterRepository): Response
    {
        $story = $chapter->getStory();

        if (!$story->isStatus() && $story->getUserId() !== $this->getUser()?->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour voir ce chapitre');
        }

        $previousChapter = $chapterRepository->findPreviousChapter($chapter);
        $nextChapter = $chapterRepository->findNextChapter($chapter);

        return $this->render('pages/chapter/read.html.twig', [
            'chapter' => $chapter,
            'story' => $story,
            'previousChapter' => $previousChapter,
            'nextChapter' => $nextChapter,
        ]);
    }

    #[Route('/story/{storyId}/read', name: 'app_story_read_first_chapter', methods: ['GET'])]
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

    #[Route('/{id}/edit', name: 'app_chapter_edit', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
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
            return $this->redirectToRoute('app_story_chapters', ['id' => $story->getId()]);
        }

        return $this->render('pages/chapter/edit.html.twig', [
            'chapter' => $chapter,
            'story' => $story,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_chapter_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
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

        return $this->redirectToRoute('app_story_chapters', ['id' => $story->getId()]);
    }

    #[Route('/{id}/move/{direction}', name: 'app_chapter_move', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
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

        return $this->redirectToRoute('app_story_chapters', ['id' => $story->getId()]);
    }
}