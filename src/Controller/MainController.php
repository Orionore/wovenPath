<?php

namespace App\Controller;

use App\Repository\StoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function homePage(StoryRepository $storyRepository): Response
    {
        $stories = $storyRepository->findAll();

        return $this->render('pages/home.html.twig', [
            'stories' => $stories,
        ]);
    }
}