<?php

namespace App\Security;

use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class EmailVerifier
{
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;

    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string)$user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]  // Include the user ID in the URL parameters
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAt'] = $signatureComponents->getExpiresAt();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @throws VerifyEmailExceptionInterface|Exception
     */
    public function handleEmailConfirmation(Request $request): void
    {
        $userId = $request->query->get('id');
        if (!$userId) {
            throw new Exception('User ID not found in request.');
        }

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new Exception('User not found.');
        }

        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string)$user->getId(), $user->getEmail());

        $user->setVerified(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}


