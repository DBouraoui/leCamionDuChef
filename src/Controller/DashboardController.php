<?php

namespace App\Controller;

use App\Entity\Pictures;
use App\Repository\PicturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;

class DashboardController extends AbstractController
{
    public function __construct() {}

    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        Request $request,
        PicturesRepository $picturesRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ): Response
    {
        $images = $picturesRepository->findAll();

        $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/images';
        if (!file_exists($uploadDir)) {
            try {
                mkdir($uploadDir, 0777, true);
            } catch (\Exception $e) {
            }
        } elseif (!is_writable($uploadDir)) {
        }

        $form = $this->createFormBuilder()
            ->add('imageFile', FileType::class, [
                'label' => 'Image à télécharger',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG, GIF, WEBP)',
                    ])
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->addFlash('info', 'Le formulaire est soumis');

            if (!$form->isValid()) {
                $this->addFlash('error', 'Le formulaire n\'est pas valide');
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            } else {

                try {
                    $imageFile = $form->get('imageFile')->getData();

                    if (!$imageFile) {
                        $this->addFlash('error', 'Aucun fichier n\'a été téléchargé');
                    } else {
                        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                        $this->addFlash('info', 'Nouveau nom de fichier: ' . $newFilename);

                        try {
                            $imageFile->move(
                                $uploadDir,
                                $newFilename
                            );
                        } catch (\Exception $e) {
                            $this->addFlash('error', 'Erreur lors du déplacement du fichier: ' . $e->getMessage());
                        }

                        $imagePath = '/uploads/images/'.$newFilename;

                        try {
                            $picture = new Pictures();
                            $picture->setUrl($imagePath);

                            $entityManager->persist($picture);
                            $entityManager->flush();
                        } catch (\Exception $e) {
                            $this->addFlash('error', 'Erreur lors de l\'enregistrement en base de données: ' . $e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur générale: ' . $e->getMessage());
                }
            }

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'form' => $form->createView(),
            'images' => $images
        ]);
    }

    #[Route('/dashboard/delete/{id}', name: 'app_dashboard_deletepicture', methods: ['DELETE', 'POST'])]
    public function deletePicture(int $id, PicturesRepository $picturesRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $picture = $picturesRepository->find($id);

            if (!$picture) {
                return new JsonResponse(['success' => false, 'message' => 'Image non trouvée'], Response::HTTP_NOT_FOUND);
            }

            $pictureUrl = $picture->getUrl();

            $imagePath = $this->getParameter('kernel.project_dir') . '/public' . $pictureUrl;

            if (file_exists($imagePath)) {
                if (!unlink($imagePath)) {
                    throw new \Exception('Impossible de supprimer le fichier physique à l\'emplacement: ' . $imagePath);
                }
            }

            $entityManager->remove($picture);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Image supprimée avec succès',
                'id' => $id
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}