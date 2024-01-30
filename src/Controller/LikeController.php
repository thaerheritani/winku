<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
class LikeController extends AbstractController
{
    #[Route('/like', name: 'app_like')]
    public function likePost($id)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $post->setLikes($post->getLikes() + 1);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirectToRoute('view_post', ['id' => $id]);
    }
}
