<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentiareType;
use App\Repository\CommentaireRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagsController extends AbstractController
{
    #[Route('/post/{id<\d+>}', name: 'viewPost')]
    public function advert(int $id, PostRepository $postRepository, EntityManagerInterface $entityManager, CommentaireRepository $commentaireRepository, Request $request): Response
    {
        $post = $postRepository->find($id);
        $commentaire = new Commentaire();
        $commentForm = $this->createForm(CommentiareType::class, $commentaire);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $commentaire->setPost($post); // Associer le commentaire au post actuel
            $commentaire->setAuthor($this->getUser());

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $commentForms[$post->getId()] = $commentForm->createView();
            // Rediriger vers la même page après soumission du commentaire
            return $this->redirectToRoute('post/view.html.twig', ['post' => $post, 'commentForms' => $commentForms]);
        }

        $commentForms[$post->getId()] = $commentForm->createView();



        return $this->render('post/view.html.twig', [
            'post' => $post,
            'commentForms' => $commentForms,
        ]);

    }

    #[Route('/tag/{name<[a-zA-Z-]+>}', name: 'postTag')]
    public function category(string $name, TagRepository $tagRepository, PostRepository $postRepository): Response
    {
        $tag = $tagRepository->findOneBy(['name' => $name]);
        $post  = $postRepository->findBy(['tag' => $tag]);


        return $this->render('default/category.html.twig', ['name'=> $name, 'advert' => $post]);
    }





}
