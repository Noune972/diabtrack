<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EmmergencyController extends AbstractController
{
    #[Route('/emmergency', name: 'app_emmergency')]
    public function index(): Response
    {
        return $this->render('emmergency/index.html.twig', [
            'controller_name' => 'EmmergencyController',
        ]);
    }
}
