<?php

namespace App\Controller;

use App\Entity\FindUs;
use App\Form\FinUsType;
use App\Repository\FindUsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FindUsController extends AbstractController
{
    #[Route('/find/us', name: 'app_find_us')]
    public function index(FindUsRepository $findUsRepository): Response
    {
        $findUs = $findUsRepository->findAll();

        return $this->render('find_us/index.html.twig', [
            'schedule' => $findUs,
        ]);
    }

    #[Route('/dashboard/findUs', name: 'app_dashboard_find_us', methods: ['GET','POST'])]
    public function dashboardFindUs(Request $request, EntityManagerInterface $entityManager, FindUsRepository $findUsRepository): Response
    {
        $schedule = $findUsRepository->findAll();

        try {
            $form = $this->createForm(FinUsType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $days = new FindUs();
                $days->setDay($data->getDay());
                $days->setMorningLocalisation($data->getMorningLocalisation());
                $days->setMorningTime($data->getMorningTime());
                $days->setEveningLocalisation($data->getEveningLocalisation());
                $days->setEveningTime($data->getEveningTime());
                $days->setCreatedAt(new \DateTimeImmutable("now"));

                $entityManager->persist($days);
                $entityManager->flush();
                $this->addFlash("success", 'Les horraires on été ajouter !');

              return $this->redirectToRoute('app_dashboard_find_us');
            }

            return $this->render('dashboard/findUs.html.twig', [
                'form' => $form->createView(),
                'schedule' => $schedule,
            ]);
        } catch(\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
        #[Route('/dashboard/findUs/{id}', name: 'app_dashboard_delete__find_us', methods: ['DELETE'])]
        public function deleteFindUs(int $id, EntityManagerInterface $entityManager, FindUsRepository $findUsRepository): Response
        {
            try {
               $find = $findUsRepository->find($id);

                $entityManager->remove($find);
                $entityManager->flush();

                return new JsonResponse(['success' => true]);
            } catch(\Exception $e) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()],Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        }
}
