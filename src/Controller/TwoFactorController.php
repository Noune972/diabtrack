<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class TwoFactorController extends AbstractController
{
    public function form(): Response
    {
        return $this->render('security/2fa_form.html.twig');
    }
}