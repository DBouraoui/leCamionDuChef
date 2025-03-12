<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EvenementielController extends AbstractController
{
    #[Route('/evenementiel', name: 'app_evenementiel')]
    public function index(): Response
    {
        return $this->render('evenementiel/index.html.twig', [
            'controller_name' => 'EvenementielController',
        ]);
    }
}
