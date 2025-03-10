<?php

namespace App\Controller;

use App\Repository\DishesRepository;
use App\Repository\PicturesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PicturesRepository $picturesRepository, DishesRepository $dishesRepository): Response
    {
        $pictures = $picturesRepository->findAll();

        $dishes = $dishesRepository->findBy([],null,3);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            "pictures"=> $pictures,
            'dishes' => $dishes
        ]);
    }
}
