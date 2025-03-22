<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AlerteController extends AbstractController
{
    #[Route('/alerte', name: 'app_alerte')]
    public function index(): Response
    {
        return $this->render('alerte/index.html.twig', [
            'controller_name' => 'AlerteController',
        ]);
    }
}
