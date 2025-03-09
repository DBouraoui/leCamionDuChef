<?php

namespace App\Controller;

use App\Entity\Dishes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DishesController extends AbstractController
{
    #[Route('/dishes', name: 'app_dishes')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $menu = $entityManager->getRepository(Dishes::class)->findAll();

        return $this->render('dishes/index.html.twig', [
            'controller_name' => 'DishesController',
            'menu' => $menu,
        ]);
    }
}
