<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Form\FormError;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setVerified(false);

            try {
                $entityManager->persist($user);
                $entityManager->flush();

                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('mailer@wovenpaths.com', 'Woven Paths Mail Bot'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('pages/registration/confirmation_email.html.twig')
                );

                return $this->redirectToRoute('app_check_email');
            } catch (UniqueConstraintViolationException $e) {
                $form->get('email')->addError(new FormError('This email is already used.'));

                $this->addFlash('error', 'This email address is already registered.');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('error', 'Unable to send verification email. Please try again later.');

                if ($user->getId()) {
                    $entityManager->remove($user);
                    $entityManager->flush();
                }
            }
        }

        return $this->render('pages/registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify_email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        try {
            $this->emailVerifier->handleEmailConfirmation($request);
        } catch (Exception) {
            $this->addFlash('error', 'An unexpected error occurred. Please try again.');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        return $this->render('pages/registration/check_email.html.twig');
    }
}