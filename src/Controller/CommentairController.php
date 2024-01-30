<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Form\CommentiareType;
use App\Repository\CommentaireRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentairController extends AbstractController
{
    #[Route('/commentair/{id<\d+>}', name: 'app_commentair')]
    #[IsGranted('ROLE_USER')]
    public function index(CommentaireRepository $commentaireRepository , Request $request,PostRepository $postRepository, EntityManagerInterface $em, int $id): Response
    {
        $post = $postRepository->find($id);
        $com = $commentaireRepository->findBy(['post' => $id]);


        $commentaire = new Commentaire();
        $commentForms = $this->createForm(CommentiareType::class, $commentaire);
        $commentForms->handleRequest($request);

        if ($commentForms->isSubmitted() && $commentForms->isValid()) {
            $user = $this->getUser();
            $commentaire->setPost($post);
            $commentaire->setAuthor($user);
            $em->persist($commentaire);
            $em->flush();
            $this->addFlash('success', 'Commentaire added successfully!');
            return $this->redirectToRoute('app_commentair',['id' => $post->getId()]);
        }

        return $this->render('commentair/index.html.twig', [
            'post' => $post,
            'commentForms' => $commentForms->createView(),
            'comments' => $com,
        ]);
    }
}
