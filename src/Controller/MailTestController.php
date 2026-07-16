<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class MailTestController extends AbstractController
{
    #[Route('/test-mail', name: 'app_test_mail')]
    public function index(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('nonetheless338@gmail.com')
            ->to('nathaliecamiul@yahoo.fr')
            ->subject('Test DiabTrack')
            ->html('
                <h1 style="color:#1558A8">DiabTrack</h1>

                <p>Si vous recevez cet email, Gmail SMTP fonctionne correctement.</p>
            ');

        $mailer->send($email);

        return new Response('Email envoyé avec succès.');
    }
}
