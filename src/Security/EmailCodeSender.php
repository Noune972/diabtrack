<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailCodeSender
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $em,
        private EmailCodeGenerator $generator
    ) {
    }

    public function send(User $user): void
    {
        $code = $this->generator->generate();

        $user->setEmailAuthCode($code);
        $user->setEmailAuthCodeCreatedAt(new \DateTime());

        $this->em->flush();

        $email = (new Email())
            ->from('no-reply@diabtrack.fr')
            ->to($user->getEmail())
            ->subject('Votre code de connexion DiabTrack')
            ->html("
                <h2>Connexion sécurisée</h2>

                <p>Votre code est :</p>

                <h1 style='font-size:42px;color:#1558A8'>{$code}</h1>

                <p>Ce code expire dans 10 minutes.</p>
            ");

        $this->mailer->send($email);
    }
}