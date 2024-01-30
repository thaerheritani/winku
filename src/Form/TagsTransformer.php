<?php

namespace App\Form;

use App\Entity\Tag;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;

class TagsTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value || !$value instanceof Collection) {
            return '';
        }

        return implode(',', $value->map(function (Tag $tag) {
            return $tag->getName();
        })->toArray());
    }

    public function reverseTransform($value)
    {
        $tagNames = explode(',', $value);

        return array_map(function ($tagName) {
            $tag = new Tag();
            $tag->setName($tagName);
            return $tag;
        }, $tagNames);
    }
}







/*public function transform($value)
{
    dump('transform');
    dump($value);
    return implode(",",$value->toArray());
}
public function reverseTransform($value)
{
    dump('reverse transform');
    dump($value);
    $tags = array_map(function($name) {
        $tag = new Tag();
        $tag->setName($name);
        return $tag;
    }, explode(",", $value));

    return new ArrayCollection($tags);
}*/