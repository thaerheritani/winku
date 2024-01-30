<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Like;
use App\Entity\Post;
use App\Form\CommentiareType;
use App\Repository\CommentaireRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;



class AccuilController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        PostRepository $postRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator,
    ): Response {
        $postsQuery = $postRepository->findAll();
            $user = $this->getUser();
        $pagination = $paginator->paginate(
            $postsQuery,
            $request->query->getInt('page', 1),
            3
        );
        return $this->render('accuil/home.html.twig', [
            'pagination' => $pagination,

        ]);
    }






    #[Route('/post/{id<\d+>}', name: 'view')]
    public function advert(int $id, PostRepository $postRepository, Request $request): Response
    {
        $post = $postRepository->find($id);
        $comment = new Commentaire();
        $form = $this->createForm(CommentiareType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
            $comment->setPost($post);
            // Récupérer le nom de l'utilisateur à partir de la session utilisateur
            $author = $this->getUser();

            $comment->setAuthor($author);
            $em->persist($comment);
            $em->flush();
            return $this->render('accuil/view.html.twig', ['post' => $post]);
        }

        return $this->redirectToRoute('accuil/view.html.twig', ['posts' => $post]);
        }




}




/*#[Route('/', name: 'home')]
    public function index(PostRepository $postRepository, EntityManagerInterface $entityManager, CommentaireRepository $commentaireRepository, Request $request): Response
{
    $posts = $postRepository->findAll();

    foreach ($posts as $post) {
        $commentaire = new Commentaire();
        $commentForm = $this->createForm(CommentiareType::class, $commentaire);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $commentaire->setPost($post); // Associer le commentaire au post actuel
            $commentaire->setAuthor($this->getUser());

            $entityManager->persist($commentaire);
            $entityManager->flush();

            // Rediriger vers la même page après soumission du commentaire
            return $this->redirectToRoute('home');
        }

        $commentForms[$post->getId()] = $commentForm->createView();
    }

    return $this->render('accuil/home.html.twig', [
        'post' => $posts,
        'commentForms' => $commentForms,
    ]);
}*/


