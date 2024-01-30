<?php

namespace App\Listener;

use App\Entity\Advert;
use App\Entity\Post;
use App\twig\MyTwigExtension;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class PostListener
{

    public function __construct(public MyTwigExtension $twigExtension){}

    public function preUpdate(Post $post, LifecycleEventArgs $args) {

        $post->setCreatedDate(new \DateTime());

    }




}