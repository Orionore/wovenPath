<?php

namespace App\Controller;

use App\Entity\Story;
use App\Form\MainStoryType;
use App\Repository\ChapterRepository;
use App\Repository\StoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Public controller for reading stories - no authentication required
 */
class MainStoryController extends AbstractController
{
    /**
     * Display homepage with list of published stories
     */
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(Request $request, StoryRepository $storyRepository): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 10;

        $form = $this->createForm(MainStoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $genre = $data['genre'] ?? null;
            $query = $data['query'] ?? null;

            $stories = $storyRepository->searchStories($query, $genre, $page, $limit);
        } else {
            $stories = $storyRepository->findLatestPublished($page, $limit);
        }

        $totalStories = count($stories);

        return $this->render('pages/home.html.twig', [
            'stories' => $stories,
            'totalStories' => $totalStories,
            'totalPages' => ceil($totalStories / $limit),
            'currentPage' => $page,
            'form' => $form,
        ]);
    }

    /**
     * Display a single story with its details and chapter list
     */
    #[Route('/stories/{id}', name: 'app_story_show', methods: ['GET'])]
    public function show(Story $story, ChapterRepository $chapterRepository): Response
    {
        // Only allow viewing published stories unless the user is the author
        if (!$story->isStatus()) {
            if (!$this->getUser() || $story->getUserId() !== $this->getUser()->getId()) {
                throw $this->createAccessDeniedException('Cette histoire n\'est pas encore publiée.');
            }
        }

        $chapters = $chapterRepository->findByStory($story);
        $hasChapters = count($chapters) > 0;

        return $this->render('pages/story/show.html.twig', [
            'story' => $story,
            'chapters' => $chapters,
            'hasChapters' => $hasChapters,
        ]);
    }
}