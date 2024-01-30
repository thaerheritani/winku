<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use App\Upload\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;


class PostController extends AbstractController
{


    #[Route('/create_post', name: 'create_post')]
    public function createPost(
        TagRepository $tagRepository,
        Request $request,
        FileUploader $fileUploader,
        EntityManagerInterface $entityManager
    ): Response {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                foreach ($form->get('gallery')->get('photos') as $photoForm) {
                    $fileUploader->uploadFilesFromForm($photoForm);
                    foreach ($post->getGallery()->getPhotos() as $photo) {
                        $entityManager->persist($photo);
                    }
                }

                // Gestion des tags
                $tags = $form->get('tags')->getData();
                foreach ($tags as $tagEntity) {
                    $existingTag = $tagRepository->findOneBy(['name' => $tagEntity->getName()]);
                    if (!$existingTag) {
                        $entityManager->persist($tagEntity);
                    } else {
                        $post->removeTag($tagEntity);
                        $post->addTag($existingTag);
                    }
                }

                // Configuration de l'auteur et de la date
                $post->setAuthor($this->getUser());
                $post->setCreatedDate(new \DateTime());

                $entityManager->persist($post);
                $entityManager->flush();

                return $this->redirectToRoute('home', ['post' => $post]);
            } catch (FileException $e) {
                $this->addFlash('error', 'La taille du fichier est trop grande.');
                return $this->redirectToRoute('create_post');
            }
        }

        return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }



    public function editPost(
        int $id,
        TagRepository $tagRepository,
        Request $request,
        FileUploader $fileUploader,
        EntityManagerInterface $entityManager
    ): Response {
        $post = $entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post non trouvé pour l\'id ' . $id);
        }

        $originalTags = $post->getTags()->toArray();
        $originalPhotos = $post->getGallery()->getPhotos()->toArray();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                foreach ($form->get('gallery')->get('photos') as $photoForm) {
                    $fileUploader->uploadFilesFromForm($photoForm);
                    foreach ($post->getGallery()->getPhotos() as $photo) {
                        $photo->setGallery($post->getGallery());
                        $entityManager->persist($photo);
                    }
                }

                $newTags = $form->get('tags')->getData();
                foreach ($originalTags as $tag) {
                    if (!$post->getTags()->contains($tag)) {
                        $entityManager->remove($tag);
                    }
                }

                foreach ($newTags as $tagEntity) {
                    $existingTag = $tagRepository->findOneBy(['name' => $tagEntity->getName()]);
                    if (!$existingTag) {
                        $entityManager->persist($tagEntity);
                        $post->addTag($tagEntity);
                    } else {
                        $post->addTag($existingTag);
                    }
                }



                foreach ($originalPhotos as $photo) {
                    if (!$post->getGallery()->getPhotos()->contains($photo)) {
                        $entityManager->remove($photo);
                    }
                }

                $post->setDescription($form->get('description')->getData());
                $post->setUpdateDate(new \DateTime());

                // Assurez-vous que la galerie associée au formulaire est attribuée au post
                $gallery = $form->get('gallery')->getData();
                $post->setGallery($gallery);

                $entityManager->persist($post);
                $entityManager->flush();

                return $this->redirectToRoute('home', ['post' => $post->getId()]);
            } catch (FileException $e) {
                $this->addFlash('error', 'La taille du fichier est trop grande.');
                return $this->redirectToRoute('edit_post', ['id' => $id]);
            }
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }




    #[Route('/delete_post/{id}', name: 'delete_post')]
    public function deletePost(Post $post, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();

        if ($currentUser !== $post->getAuthor()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce post.');
        }
        $comments = $post->getCommentaires();
        foreach ($comments as $comment) {
            $entityManager->remove($comment);
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }



}






