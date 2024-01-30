<?php

namespace App\Controller;

use App\Form\SearchUserType;
use App\Form\TagSearchType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'user_search')]
    public function search(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(SearchUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->get('pseudo')->getData();
            $users = $userRepository->findByPseudo($pseudo);

            return $this->render('search/index.html.twig', [
                'users' => $users,
                'searchForm' => $form->createView(),
            ]);
        }

        $users = []; // Initialisation de la variable users à un tableau vide si le formulaire n'est pas soumis ou invalide

        return $this->render('search/index.html.twig', [
            'users' => $users,
            'searchForm' => $form->createView(),
        ]);
    }


    #[Route('/search-by-tags', name: 'search_by_tags')]
    public function searchByTags(Request $request, PostRepository $postRepository): Response
    {
        $form = $this->createForm(TagSearchType::class);
        $form->handleRequest($request);

        $posts = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->get('tag')->getData();
            $posts = $postRepository->findPostsByTag($tag);
        }

        return $this->render('search/tag.html.twig', [
            'posts' => $posts,
            'tagSearchForm' => $form->createView(),
        ]);
    }





}
