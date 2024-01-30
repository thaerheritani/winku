<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Entity\User;
use App\Form\EditProfileType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($hashedPassword);

            // Créez un profil pour cet utilisateur avec des valeurs par défaut pour photo et cover
            $profil = new Profil();
            $profil->setUser($user);
            $profil->setPhoto('z-65859d3e44578.jpg'); // Remplacez 'default_photo.jpg' par le chemin de votre photo par défaut
            $profil->setCover('z-65859d3e44578.jpg'); // Remplacez 'default_cover.jpg' par le chemin de votre cover par défaut

            // Persistez l'utilisateur et son profil dans la base de données
            $entityManager->persist($user);
            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }


    #[Route('/user/{id}/editProfile', name: 'editProfile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Gérer le cas où l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        $profil = $user->getProfil(); // Assurez-vous d'adapter cela en fonction de votre relation entre User et Profil

        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Utilisez l'injection de dépendances pour accéder à l'EntityManager

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
        }

        return $this->render('profil/editProfile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'profil' => $profil, // Ajoutez cette ligne pour passer le profil au template
        ]);
    }

}


