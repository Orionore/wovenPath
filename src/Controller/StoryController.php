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

#[Route('/profile/stories')]
#[IsGranted('ROLE_USER')]
class StoryController extends AbstractController
{
    #[Route('/', name: 'app_profile_stories_index', methods: ['GET'])]
    public function index(StoryRepository $storyRepository): Response
    {
        $user = $this->getUser();
        $stories = $storyRepository->findBy(['user_id' => $user->getId()]);

        return $this->render('pages/profile/stories/index.html.twig', [
            'stories' => $stories,
        ]);
    }

    #[Route('/new', name: 'app_profile_stories_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        MediaService $mediaService
    ): Response
    {
        $story = new Story();
        $story->setUserId($this->getUser()->getId());
        $story->setStatus(false);

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

                    return $this->render('pages/profile/stories/new.html.twig', [
                        'story' => $story,
                        'form' => $form,
                    ]);
                }
            }

            $entityManager->persist($story);
            $entityManager->flush();

            $this->addFlash('success', 'Votre histoire a été créée avec succès.');

            return $this->redirectToRoute('app_profile_stories_index');
        }

        return $this->render('pages/profile/stories/new.html.twig', [
            'story' => $story,
            'form' => $form,
        ]);
    }

    #[Route('/story/{id}', name: 'app_story_show', methods: ['GET'])]
    public function showStory(Story $story, ChapterRepository $chapterRepository): Response
    {
        if (!$story->isStatus() && $story->getUserId() !== $this->getUser()?->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour voir cette histoire');
        }

        $chapters = $chapterRepository->findByStory($story);
        $hasChapters = count($chapters) > 0;

        return $this->render('pages/profile/stories/show.html.twig', [
            'story' => $story,
            'chapters' => $chapters,
            'hasChapters' => $hasChapters,
        ]);
    }

    #[Route('/{id}', name: 'app_profile_stories_show', methods: ['GET'])]
    public function show(Story $story, ChapterRepository $chapterRepository): Response
    {
        if (!$story->isStatus() && $story->getUserId() !== $this->getUser()?->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les permissions pour voir cette histoire');
        }

        $chapters = $chapterRepository->findByStory($story);
        $hasChapters = count($chapters) > 0;

        return $this->render('pages/profile/stories/show.html.twig', [
            'story' => $story,
            'chapters' => $chapters,
            'hasChapters' => $hasChapters,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profile_stories_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Story $story,
        EntityManagerInterface $entityManager,
        MediaService $mediaService
    ): Response
    {
        $this->denyAccessUnlessGranted('edit', $story);

        $form = $this->createForm(StoryType::class, $story);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                try {
                    // Suppression de l'ancienne image si elle existe
                    if ($story->getImageName()) {
                        $mediaService->deleteImage($story->getImageName());
                    }

                    // Traitement et enregistrement de la nouvelle image
                    $imageName = $mediaService->processAndSaveImage($imageFile);
                    $story->setImageName($imageName);
                } catch (Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du traitement de l\'image : ' . $e->getMessage());

                    return $this->render('pages/profile/stories/edit.html.twig', [
                        'story' => $story,
                        'form' => $form,
                    ]);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre histoire a été mise à jour avec succès.');

            return $this->redirectToRoute('app_profile_stories_index');
        }

        return $this->render('pages/profile/stories/edit.html.twig', [
            'story' => $story,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profile_stories_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Story $story,
        EntityManagerInterface $entityManager,
        MediaService $mediaService
    ): Response
    {
        $this->denyAccessUnlessGranted('delete', $story);

        if ($this->isCsrfTokenValid('delete'.$story->getId(), $request->request->get('_token'))) {
            if ($story->getImageName()) {
                $mediaService->deleteImage($story->getImageName());
            }

            $story->setDeletedAt(new DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Histoire supprimée avec succès.');
        }

        return $this->redirectToRoute('app_profile_stories_index');
    }

    #[Route('/{id}/publish', name: 'app_profile_stories_publish', methods: ['POST'])]
    public function publish(
        Request $request,
        Story $story,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('edit', $story);

        if ($this->isCsrfTokenValid('publish'.$story->getId(), $request->request->get('_token'))) {
            $story->setStatus(true);
            $entityManager->flush();

            $this->addFlash('success', 'Votre histoire a été publiée avec succès.');
        }

        return $this->redirectToRoute('app_profile_stories_show', ['id' => $story->getId()]);
    }

    #[Route('/{id}/unpublish', name: 'app_profile_stories_unpublish', methods: ['POST'])]
    public function unpublish(
        Request $request,
        Story $story,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('edit', $story);

        if ($this->isCsrfTokenValid('unpublish'.$story->getId(), $request->request->get('_token'))) {
            $story->setStatus(false);
            $entityManager->flush();

            $this->addFlash('success', 'Votre histoire a été retirée de la publication.');
        }

        return $this->redirectToRoute('app_profile_stories_show', ['id' => $story->getId()]);
    }
}