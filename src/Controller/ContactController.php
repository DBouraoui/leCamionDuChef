<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $form = $this->createForm(ContactType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $contact = $form->getData();

                $contactModel = new Contact();

                $contactModel->setName($contact->getName());
                $contactModel->setEmail($contact->getEmail());
                $contactModel->setPhone($contact->getPhone());
                $contactModel->setMessage($contact->getMessage());
                $contactModel->setSubject($contact->getSubject());

                $entityManager->persist($contactModel);
                $entityManager->flush();

                $this->addFlash('success', 'Le message a bien étais envoyés');

                return $this->redirectToRoute('app_contact');

            }

            return $this->render('contact/index.html.twig', [
                'controller_name' => 'ContactController',
                'form'=>$form
            ]);
        } catch (\Exception $exception) {
            return new JsonResponse(['success'=>false, 'message'=>$exception->getMessage()],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
