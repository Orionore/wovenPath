<?php

namespace App\Controller;

use App\Entity\Story;
use App\Form\StoryType;
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
    public function showStory(Story $story): Response
    {
        return $this->render('pages/profile/stories/index.html.twig', [
            'story' => $story,
        ]);
    }

    #[Route('/{id}', name: 'app_profile_stories_show', methods: ['GET'])]
    public function show(Story $story): Response
    {
        $this->denyAccessUnlessGranted('view', $story);

        return $this->render('pages/profile/stories/show.html.twig', [
            'story' => $story,
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
                    // Supprimer l'ancienne image si elle existe
                    if ($story->getImageName()) {
                        $mediaService->deleteImage($story->getImageName());
                    }

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
            // Si vous voulez supprimer l'image lors d'une suppression logique
            if ($story->getImageName()) {
                $mediaService->deleteImage($story->getImageName());
            }

            // Soft delete en mettant à jour deletedAt
            $story->setDeletedAt(new DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Histoire supprimée avec succès.');
        }

        return $this->redirectToRoute('app_profile_stories_index');
    }
}