<?php

namespace App\Controller;

use App\Repository\PicturesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PicturesRepository $picturesRepository): Response
    {
        $pictures = $picturesRepository->findAll();


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            "pictures"=> $pictures,
        ]);
    }
}
