<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageUploadManager extends AbstractController
{

    /**
     * Rename and save trick images in uploads/images directory
     */
    public function imageFile($uploadedFile)
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $filename = $safeFilename . '_' . uniqid() . '_' . $uploadedFile->guessExtension();
        try {
            $uploadedFile->move($this->getParameter('image_directory'), $filename);
        } catch (FileException $e) {
            die('Erreur, impossible de sauvegarder l\'image');
        }
        return $filename;
    }

    /**
     * Rename and save user avatar image in uploads/avatars directory
    */
    public function avatarFile($avatarFile, $user)
    {
        $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
        $renamedFilename = $user->getFirstName() . '_' . $user->getLastName() . '_' . $originalFilename;
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $renamedFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();
        try {
            $avatarFile->move($this->getParameter('avatar_directory'), $newFilename);
        } catch (FileException $e) {
            die('Erreur, impossible de sauvegarder l\'avatar');
        }
       return $newFilename;
    }
}