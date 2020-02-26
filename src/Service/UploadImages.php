<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UploadImages extends AbstractController
{
    public function upload($image)
    {
        $originalFilename = pathinfo($image->getImageFile()->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $filename = $safeFilename . '_' . uniqid() . '_' . $image->getImageFile()->guessExtension();
        try {
            $image->getImageFile()->move($this->getParameter('image_directory'), $filename);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

    }
}