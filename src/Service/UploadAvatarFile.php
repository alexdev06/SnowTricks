<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadAvatarFile extends AbstractController
{
    public function uploadAvatarFile($avatarFile, User $user)
    {
        $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
        $renamedFilename = $user->getFirstName() . '_' . $user->getLastName() . '_' . $originalFilename;
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $renamedFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();
        try {
            $avatarFile->move($this->getParameter('avatar_directory'), $newFilename);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        $user->setAvatar($newFilename);
    }

}