<?php

namespace App\Controller;

use App\Entity\Story;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoriesController extends AbstractController
{
    #[Route('/story/{id}', name: 'app_story_show')]
    public function homePage(Story $story): Response
    {

        return $this->render('pages/home.html.twig', [
            'story' => $story,
        ]);
    }
}
