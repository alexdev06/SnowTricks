<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use App\Service\ImageUploadManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * Homepage with tricks list
     * 
     * @Route("/", name="homepage")
     */
    public function index(TrickRepository $repo)
    {
        $tricks = $repo->findBy([],[
            'createdAt' => 'DESC'
            ]);

        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * Homepage more tricks display by step of 12
     * 
     * @Route("/{start}", name="more_tricks", requirements={"start": "\d+"})
     */
    public function moreTricks(TrickRepository $repo, $start = 12)
    {
        $tricks = $repo->findBy([], [
            'createdAt' => 'DESC'
        ], 12, $start);

        return $this->render('trick/moreTricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * Trick page
     * 
     * @Route("/trick/{slug}", name="trick_show")
     * 
     */
    public function show(Trick $trick, $slug, Request $request, EntityManagerInterface $manager)
    {
        // Comment form for registered user
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setUser($this->getUser());
            $comment->setTrick($trick);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('trick_show',
             ['slug' => $slug]
            );
        }
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form'  => $form->createView()
        ]);
    }

    /**
     * Display more comments by step of 10 in Trick page
     * 
     * @Route("/trick/{slug}/{start}", name="more_comments", requirements={"start": "\d+"})
     */
    public function moreComments(TrickRepository $repo, $slug, $start = 10)
    {
        $trick = $repo->findOneBySlug($slug);
        return $this->render('trick/moreComments.html.twig', [
            'trick' => $trick,
            'start' => $start
        ]);
    }

    /**
     * Trick creation page
     * 
     * @Route("/create", name="trick_create")
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, EntityManagerInterface $manager, ImageUploadManager $imageUploadManager)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageMainFile */
            $uploadedFile = $form->get('imageMainFile')->getData();
            if ($uploadedFile) {
                // ImageUploadManager service which renames and saves trick imageMain in the good path
                $filename = $imageUploadManager->imageSave($uploadedFile, ImageUploadManager::IMAGE_DIRECTORY);
                $trick->setImageMain($filename);
            }
            // Video links treatment
            foreach ($trick->getVideos() as $video) {
                if ($video) {
                    $video->setTrick($trick);

                    $manager->persist($video);
                }
            }
            // Images treatement
            foreach ($trick->getImages() as $image) {
                if ($image) {
                    // ImageUploadManager service which renames and saves trick images
                    $filename = $imageUploadManager->imageSave($image->getImageFile(), ImageUploadManager::IMAGE_DIRECTORY);
                    $image->setFilename($filename);
                    $image->setTrick($trick);

                    $manager->persist($image);
                }
            }

            $trick->setUser($this->getUser());
    
            $manager->persist($trick);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le trick ' . $trick->getName() . ' a été enregistré avec succès !'
            );
            return $this->redirectToRoute('homepage');
        }
        return $this->render('trick/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Trick edition page
     * 
     * @Route("/trick/{slug}/edit", name="trick_edit")
     * @IsGranted("ROLE_USER")
     */
    public function edit(Trick $trick, Request $request, EntityManagerInterface $manager, ImageUploadManager $imageUploadManager)
    {
        // Save the imageMain file name in $imageMain variable and add the complet file path of the imageMain file.
        // Its necessary to make form working.
        $imageMain = $trick->getImageMain();
        if ($imageMain !== null) {
            $trick->setImageMain(new File($this->getParameter('image_directory') . '/' . $imageMain));
        }
     
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile*/
            $uploadedFile = $form->get('imageMainFile')->getData();
            // Check if a new imageMain has been added in the form
            if ($uploadedFile) {
                // ImageUploadManager service which renames and saves trick imageMain
                $filename = $imageUploadManager->imageSave($uploadedFile, ImageUploadManager::IMAGE_DIRECTORY);
                $trick->setImageMain($filename);
            } else {
                // If imageMain has not been changed, imageMain value previously saved in $imageMain is reintegrated in the trick
                $trick->setImageMain($imageMain);
            }
            // Video links treatment
            foreach ($trick->getVideos() as $video) {
                if ($video) {
                    $video->setTrick($trick);
                    $manager->persist($video);
                }
            }
            // Images treatement
            $uploadedCollection = $form->get('images')->getData();
            foreach ($uploadedCollection as $image) {
                if ($image->getImageFile()) {
                   // ImageUploadManager service which renames and saves trick images
                    $filename = $imageUploadManager->imageSave($image->getImageFile(), ImageUploadManager::IMAGE_DIRECTORY);
                    $image->setFilename($filename);
                    $image->setTrick($trick);
                    $manager->persist($image);
                }
            }

            $trick->setUser($this->getUser());

            $manager->persist($trick);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le trick ' . $trick->getName() . ' a été modifié avec succès !'
            );
            return $this->redirectToRoute('homepage');
        }
        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            'imageMain' => $imageMain
        ]);
    }

    /**
     * Trick remove action
     * 
     * @Route("/trick/{slug}/delete", name="trick_delete")
     * @IsGranted("ROLE_USER")
     */
    public function delete(Trick $trick, EntityManagerInterface $manager)
    {
            $manager->remove($trick);
            $manager->flush();

            $this->addFlash(
                "success",
                "Le trick a bien été supprimé"
            );

            return $this->redirectToRoute('homepage');
    }

    /**
     * Delete trick images in database and remove image files
     * 
     * @Route("/image{id}", name="image_remove")
     * @IsGranted("ROLE_USER")
     */
    public function removeImage(Image $image, EntityManagerInterface $manager, ImageRepository $imageRepo)
    {
        $image = $imageRepo->findOneById($image);
        $manager->remove($image);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'L\'image a bien été supprimée !'
        ], 200);
    }

    /**
     * Trick video delete
     * 
     * @Route("/video{id}", name="video_remove")
     * @IsGranted("ROLE_USER")
     */
    public function removeVideo(Video $video, EntityManagerInterface $manager, VideoRepository $videoRepo)
    {
        $video = $videoRepo->findOneById($video);
        $manager->remove($video);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'La video a bien été supprimée !'
        ], 200);
    }
}
