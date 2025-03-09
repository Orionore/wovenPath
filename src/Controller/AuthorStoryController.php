<?php

namespace App\Controller;

use App\Entity\Story;
use App\Form\StoryType;
use App\Repository\ChapterRepository;
use App\Repository\StoryRepository;
use App\Service\MediaService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for managing stories by their authors
 */
#[Route('/author/stories')]
#[IsGranted('ROLE_USER')]
class AuthorStoryController extends AbstractController
{
    /**
     * List all stories created by the authenticated user
     */
    #[Route('/', name: 'app_author_stories_index', methods: ['GET'])]
    public function index(StoryRepository $storyRepository): Response
    {
        $user = $this->getUser();
        $stories = $storyRepository->findAllByUser($user->getId());

        return $this->render('pages/author/stories/index.html.twig', [
            'stories' => $stories,
        ]);
    }

    /**
     * Create a new story
     */
    #[Route('/new', name: 'app_author_stories_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        MediaService $mediaService
    ): Response
    {
        $story = new Story();
        $story->setUserId($this->getUser()->getId());
        $story->setStatus(false); // Default to draft mode

        $form = $this->createForm(StoryType::class, $story);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                try {
                    $imageName = $mediaService->processAndSaveImage($imageFile);
                    $story->setImageName($imageName);
                } catch (Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du traitement de l\'image : ' . $e->getMessage());

                    return $this->render('pages/author/stories/new.html.twig', [
                        'story' => $story,
                        'form' => $form,
                    ]);
                }
            }

            $entityManager->persist($story);
            $entityManager->flush();

            $this->addFlash('success', 'Votre histoire a été créée avec succès.');

            return $this->redirectToRoute('app_author_stories_index');
        }

        return $this->render('pages/author/stories/new.html.twig', [
            'story' => $story,
            'form' => $form,
        ]);
    }

    /**
     * Show detailed view of a story with management options
     */
    #[Route('/{id}', name: 'app_author_stories_show', methods: ['GET'])]
    public function show(Story $story, ChapterRepository $chapterRepository): Response
    {
        // Ensure user is the author of the story
        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour gérer cette histoire');
        }

        $chapters = $chapterRepository->findByStory($story);
        $hasChapters = count($chapters) > 0;

        return $this->render('pages/author/stories/show.html.twig', [
            'story' => $story,
            'chapters' => $chapters,
            'hasChapters' => $hasChapters,
        ]);
    }

    /**
     * Edit an existing story
     */
    #[Route('/{id}/edit', name: 'app_author_stories_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Story $story,
        EntityManagerInterface $entityManager,
        MediaService $mediaService
    ): Response
    {
        // Ensure user is the author of the story
        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour modifier cette histoire');
        }

        $form = $this->createForm(StoryType::class, $story);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                try {
                    // Delete the old image if it exists
                    if ($story->getImageName()) {
                        $mediaService->deleteImage($story->getImageName());
                    }

                    // Process and save the new image
                    $imageName = $mediaService->processAndSaveImage($imageFile);
                    $story->setImageName($imageName);
                } catch (Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du traitement de l\'image : ' . $e->getMessage());

                    return $this->render('pages/author/stories/edit.html.twig', [
                        'story' => $story,
                        'form' => $form,
                    ]);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre histoire a été mise à jour avec succès.');

            return $this->redirectToRoute('app_author_stories_index');
        }

        return $this->render('pages/author/stories/edit.html.twig', [
            'story' => $story,
            'form' => $form,
        ]);
    }

    /**
     * Delete a story (soft delete)
     */
    #[Route('/{id}/delete', name: 'app_author_stories_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Story $story,
        EntityManagerInterface $entityManager,
        MediaService $mediaService
    ): Response
    {
        // Ensure user is the author of the story
        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour supprimer cette histoire');
        }

        if ($this->isCsrfTokenValid('delete'.$story->getId(), $request->request->get('_token'))) {
            if ($story->getImageName()) {
                $mediaService->deleteImage($story->getImageName());
            }

            // Soft delete by setting deletedAt field
            $story->setDeletedAt(new DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Histoire supprimée avec succès.');
        }

        return $this->redirectToRoute('app_author_stories_index');
    }

    /**
     * Publish a story (make it visible to everyone)
     */
    #[Route('/{id}/publish', name: 'app_author_stories_publish', methods: ['POST'])]
    public function publish(
        Request $request,
        Story $story,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour publier cette histoire');
        }

        if ($this->isCsrfTokenValid('publish'.$story->getId(), $request->request->get('_token'))) {
            $story->setStatus(true);
            $entityManager->flush();

            $this->addFlash('success', 'Votre histoire a été publiée avec succès.');
        }

        return $this->redirectToRoute('app_author_stories_show', ['id' => $story->getId()]);
    }

    /**
     * Unpublish a story (make it private/draft)
     */
    #[Route('/{id}/unpublish', name: 'app_author_stories_unpublish', methods: ['POST'])]
    public function unpublish(
        Request $request,
        Story $story,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($story->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour dépublier cette histoire');
        }

        if ($this->isCsrfTokenValid('unpublish'.$story->getId(), $request->request->get('_token'))) {
            $story->setStatus(false);
            $entityManager->flush();

            $this->addFlash('success', 'Votre histoire a été retirée de la publication.');
        }

        return $this->redirectToRoute('app_author_stories_show', ['id' => $story->getId()]);
    }
}