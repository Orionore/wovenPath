<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileEditType;
use App\Repository\StoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(StoryRepository $storyRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $recentStories = $storyRepository->findBy(
            ['user_id' => $user->getId()],
            ['createdAt' => 'DESC'],
            3
        );

        return $this->render('pages/author/profile.html.twig', [
            'user' => $user,
            'recentStories' => $recentStories
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('pages/author/profile_edit.html.twig', [
            'form' => $form,
        ]);
    }
}