<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_dashboard_message')]
    public function index(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();

        return $this->render('dashboard/message.html.twig', [
            'controller_name' => 'MessageController',
            'contacts' => $contacts,
        ]);
    }

    #[Route('/message/delete/{id}', name: 'app_dashboard_message_delete', methods: ['POST'])]
    public function delete(int $id, ContactRepository $contactRepository, EntityManagerInterface $entityManager): Response
    {
        try {
                $contactRepository = $contactRepository->find($id);

                $entityManager->remove($contactRepository);
                $entityManager->flush();

                $this->addFlash('success', 'Le mesage a bie  été supprimer');
            return new JsonResponse(['success' => true], Response::HTTP_OK);
        } catch(\Exception $e)
        {
            return new Jsonresponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
