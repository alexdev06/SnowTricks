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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class TrickController extends AbstractController
{
    /**
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
     * @Route("/trick/{slug}", name="trick_show")
     */
    public function show(TrickRepository $repo, $slug, Request $request, EntityManagerInterface $manager)
    {
        $trick = $repo->findOneBySlug($slug);
       
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setUser($this->getUser());
            $comment->setTrick($trick);

            $manager->persist($comment);
            $manager->flush();
        }
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form'  => $form->createView()
        ]);
    }

    /**
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
     * @Route("/create", name="trick_create")
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageMainFile */
            $uploadedFile = $form->get('imageMainFile')->getData();
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $filename = $safeFilename . '_' . uniqid() . '_' . $uploadedFile->guessExtension();
                try {
                    $uploadedFile->move($this->getParameter('image_directory'), $filename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $trick->setImageMain($filename);
            }

            foreach ($trick->getVideos() as $video) {
                if ($video) {
                    $video->setTrick($trick);
                    $manager->persist($video);
                }
            }

            foreach ($trick->getImages() as $image) {
                if ($image) {
                    $originalFilename = pathinfo($image->getImageFile()->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $filename = $safeFilename . '_' . uniqid() . '_' . $image->getImageFile()->guessExtension();
                    try {
                        $image->getImageFile()->move($this->getParameter('image_directory'), $filename);
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $image->setTrick($trick);
                    $image->setFilename($filename); 
                    $manager->persist($image);
                }
            }

            $trick->setCreatedAt(new \DateTime());
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
     * @Route("/trick/{slug}/edit", name="trick_edit")
     * @IsGranted("ROLE_USER")
     */
    public function edit(Trick $trick, Request $request, EntityManagerInterface $manager)
    {
        // on récupère un nom de fichier qui est l'entrée en db.
        $imageMain = $trick->getImageMain();
        if ($imageMain !== null) {
            $trick->setImageMain(new File($this->getParameter('image_directory') . '/' . $imageMain));
        }
       
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile*/
            $uploadedFile = $form->get('imageMainFile')->getData();
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $filename = $safeFilename . '_' . uniqid() . '_' . $uploadedFile->guessExtension();
                
                try {
                    $uploadedFile->move($this->getParameter('image_directory'), $filename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                
                $trick->setImageMain($filename);
            } else {
                $trick->setImageMain($imageMain);
            }

            foreach ($trick->getVideos() as $video) {
                if ($video) {
                    $video->setTrick($trick);
                    $manager->persist($video);
                }
            }

            $uploadedCollection = $form->get('images')->getData();
            foreach ($uploadedCollection as $image) {
                if ($image->getImageFile()) {
                    $originalFilename = pathinfo($image->getImageFile()->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $filename = $safeFilename . '_' . uniqid() . '.' . $image->getImageFile()->guessExtension();
                    
                    try {
                        $image->getImageFile()->move($this->getParameter('image_directory'), $filename);
                        
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $image->setTrick($trick);
                    $image->setFilename($filename); 
                    $manager->persist($image);
                }
            }

            $trick->setModifiedAt(new \DateTime());
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
     * @Route("/image{id}", name="image_remove")
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
     * @Route("/video{id}", name="video_remove")
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
