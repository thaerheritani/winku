<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Form\ProfillType;
use App\Form\ProfilType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Upload\FileUploader2;
use App\Upload\FileUploader3;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    private FileUploader2 $fileUploader;
    private FileUploader3 $fileUploader3;

    public function __construct(FileUploader2 $fileUploader, FileUploader3 $fileUploader3)
    {
        $this->fileUploader = $fileUploader;
        $this->fileUploader3 = $fileUploader3;
    }

    #[Route('/user/{id}', name: 'app_profile')]
    public function showProfile(UserRepository $userRepository, string $id, PostRepository $postRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $profil = $user->getProfil();
        $post = $user->getPosts();

        return $this->render('profil/profil.html.twig', ['user' => $user, 'posts' => $post, 'profil' => $profil]);
    }


    #[Route('/profil/edit-password', name: 'app_profile_edit_password')]
    public function update_password(): Response
    {
        return $this->render('profil/update-password.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    #[Route('/user/{id}/editPhoto', name: 'editPhoto')]
    public function editProfil(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $profil = $user->getProfil();

        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->fileUploader->uploadFilesFromForm($form);

                $entityManager->persist($profil);
                $entityManager->flush();

                return $this->redirectToRoute('app_profile', ['id' => $profil->getId()]);
            }
        } catch (FileException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'profil' => $profil,
        ]);
    }






    #[Route('/user/{id}/editCover', name: 'editCover')]
    public function editProfill(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $profil = $user->getProfil();

        $form = $this->createForm(ProfillType::class, $profil);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $this->fileUploader3->uploadFilesFromForm($form);

                $entityManager->persist($profil);
                $entityManager->flush();

                return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
            }
        } catch (FileException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('profil/editt.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'profil' => $profil,
        ]);
    }




}

