<?php

namespace App\Controller;

use App\Entity\Story;
use App\Form\StoryType;
use App\Repository\StoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $story = new Story();
        $story->setUserId($this->getUser()->getId());
        $story->setStatus(false);

        $form = $this->createForm(StoryType::class, $story);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('stories_directory'),
                        $newFilename
                    );
                    $story->setImageName($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image');
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
    public function edit(Request $request, Story $story, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('edit', $story);

        $form = $this->createForm(StoryType::class, $story);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('stories_directory'),
                        $newFilename
                    );

                    // Supprimer l'ancienne image si elle existe
                    if ($story->getImageName() && file_exists($this->getParameter('stories_directory').'/'.$story->getImageName())) {
                        unlink($this->getParameter('stories_directory').'/'.$story->getImageName());
                    }

                    $story->setImageName($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image');
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
    public function delete(Request $request, Story $story, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('delete', $story);

        if ($this->isCsrfTokenValid('delete'.$story->getId(), $request->request->get('_token'))) {
            // Soft delete en mettant à jour deletedAt
            $story->setDeletedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Histoire supprimée avec succès.');
        }

        return $this->redirectToRoute('app_profile_stories_index');
    }
}