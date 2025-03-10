<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Entity\Pictures;
use App\Form\DishesType;
use App\Repository\DishesRepository;
use App\Repository\PicturesRepository;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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

    #[Route('/dashboard/carte', name: 'app_dashboard_carte', methods: ['GET', 'POST'])]
    public function carte(Request $request, EntityManagerInterface $entityManager): Response
    {

        $dish = new Dishes();

        // Utilisation de la classe de formulaire dédiée
        $form = $this->createForm(DishesType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du fichier image
            $imageFile = $form->get('imageFile')->getData();

            // Traitement du fichier image si présent
            if ($imageFile) {
                // Génération d'un nom de fichier unique
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                // Déplacement du fichier vers le répertoire configuré
                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/dishes',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion de l'erreur si le déplacement échoue
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                }

                // Stockage du nom du fichier dans l'entité
                $dish->setPictureUrl($newFilename);

                $dish->setCreatedAt(new \DateTimeImmutable('now'));
            }

            // Sauvegarde en base de données
            $entityManager->persist($dish);
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été ajouté à la carte avec succès !');

            // Redirection pour éviter la soumission multiple du formulaire
            return $this->redirectToRoute('app_dashboard_carte');
        }

        // Récupération de tous les plats existants pour les afficher
        $dishes = $entityManager->getRepository(Dishes::class)->findAll();

        return $this->render('dashboard/cartes.html.twig', [
            'form' => $form->createView(),
            'dishes' => $dishes
        ]);
    }

    #[Route('/dashboard/carte/delete/{id}', name: 'app_dashboard_carte_delete', methods: ['DELETE'])]
    public function DeleteDishes(int $id,EntityManagerInterface $entityManager, DishesRepository $dishesRepository): Response
    {
        try {
           $dishes = $dishesRepository->find($id);

           if (!$dishes) {
                $this->addFlash("error","Le menu n'a pas pue être supprimer");
           }

          $file = $this->getParameter('kernel.project_dir') . '/public/uploads/dishes/' . $dishes->getPictureUrl();

            if (file_exists($file)) {
                if (!unlink($file)) {
                    throw new \Exception('Impossible de supprimer le fichier: ' . $file);
                }
            }

            $entityManager->remove($dishes);
            $entityManager->flush();

            $this->addFlash("success","Le menu a été spprimer avec succes");

            return new JsonResponse(['success' => true], Response::HTTP_OK);
        } catch (\Exception)
        {
            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/dashboard/carte/get/{id}', name: 'app_dashboard_get_dish', methods: ['GET'])]
    public function getDish(Dishes $dish): JsonResponse
    {
        return new JsonResponse([
            'id' => $dish->getId(),
            'title' => $dish->getTitle(),
            'description' => $dish->getDescription(),
            'price' => $dish->getPrice(),
            'pictureUrl' => $dish->getPictureUrl()
        ]);
    }

    #[Route('/dashboard/carte/update/{id}', name: 'app_dashboard_update_dish', methods: ['POST'])]
    public function updateDish(Request $request, Dishes $dish, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupération des données du formulaire
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $price = $request->request->get('price');

        // Mise à jour des données du plat
        $dish->setTitle($title);
        $dish->setDescription($description);
        $dish->setPrice($price);

        $imageFile = $request->files->get('imageFile');

        if ($imageFile) {
            // Suppression de l'ancienne image
            if ($dish->getPictureUrl()) {
                $oldImagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/dishes/' . $dish->getPictureUrl();
                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Enregistrement de la nouvelle image
            $newFilename = uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('kernel.project_dir'). '/public/uploads/dishes/', $newFilename
                );
                $dish->setPictureUrl($newFilename);
            } catch (FileException $e) {
                return new JsonResponse(['success' => false, 'message' => 'Erreur lors du téléchargement de l\'image'], 500);
            }
        }

        // Sauvegarde en base de données
        $entityManager->flush();

        // Retour des données mises à jour
        return new JsonResponse([
            'success' => true,
            'message' => 'Plat modifié avec succès',
            'dish' => [
                'id' => $dish->getId(),
                'title' => $dish->getTitle(),
                'description' => $dish->getDescription(),
                'price' => $dish->getPrice(),
                'pictureUrl' => $dish->getPictureUrl()
            ]
        ]);
    }
}