<?php

namespace App\Upload;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader3
{

    public function __construct(private $postImagesPath)
    {
        $this->postImagesPath = $postImagesPath;
    }


    public function uploadFilesFromForm(Form $photoForm)
    {

        $imageFile = $photoForm->get('cover')->getData();

        if ($imageFile) {
            $maxFileSize = 1024 * 100;
            $fileSize = $imageFile->getSize();

            if ($fileSize > $maxFileSize) {
                throw new FileException('La taille du fichier est trop grande.');
            }
            $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFileName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFileName);
            $newFileName = $safeFileName . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->postImagesPath,
                    $newFileName
                );
            } catch (FileException $e) {

            }
            $photoForm->getData()->setCover($newFileName);
        }
    }

    /**
     * @return mixed
     */
    public function getPostImagesPath()
    {
        return $this->postImagesPath;
    }


}

