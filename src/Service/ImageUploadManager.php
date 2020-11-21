<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageUploadManager extends AbstractController
{
    /**
     * Constants which represents uploads directories
     */
    const AVATAR_DIRECTORY = 'avatar_directory';
    const IMAGE_DIRECTORY = 'image_directory';

    /**
     * Rename image files and save in the right directory (avatars or images)
     */
    public function imageSave($uploadedFile, $directory, $user = null)
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        // If user is not null(It's an avatar file), rename uses Firstname and Lastname of the user for the new name
        if (null !== $user) {
            $renamedFilename = $user->getFirstName() . '_' . $user->getLastName() . '_' . $originalFilename;
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $renamedFilename);
        } else {
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        }

        $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        try {
            // Files are moved and saved in the right directory
            $uploadedFile->move($this->getParameter($directory), $newFilename);
        } catch (FileException $e) {
            die('Erreur, impossible de sauvegarder l\'image');
        }

        return $newFilename;
    }
}
