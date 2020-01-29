<?php

namespace App\Controller;

use DateTime;
use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Form\ImageType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class TrickController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(TrickRepository $repo)
    {
        $tricks = $repo->findAll();

        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    
    /**
     * @Route("/trick/{slug}", name="trick_show")
     */
    public function show(TrickRepository $repo, $slug)
    {
        $trick = $repo->findOneBySlug($slug);
        return $this->render('trick/show.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/create/", name="trick_create")
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $videosCollection = $form->get('videos')->getData();

            foreach ($videosCollection as $video) {
                if ($video) {
                    $video->setTrick($trick);
                    $manager->persist($video);
                }
            }
            
            $imagesCollection = $form->get('images')->getData();

            foreach ($imagesCollection as $image) {

                if ($image) {
                    $originalFilename = pathinfo($image->getFile()->getClientOriginalName(), PATHINFO_FILENAME);
                    $renamedFilename = $trick->getName() . $originalFilename;
                    
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $renamedFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '-' . $image->getFile()->guessExtension();
                    
                    try {
                        $image->getFile()->move($this->getParameter('image_directory'), $newFilename);
                        
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    
                }
                
                $image->setTrick($trick);
                $image->setFilename($newFilename); 
                $manager->persist($image);
            }
    

            $imageMainFile = $form->get('imageMain')->getData();


            if ($imageMainFile) {
  
                $originalFilename = pathinfo($imageMainFile->getClientOriginalName(), PATHINFO_FILENAME);

                $renamedFilename = $trick->getName() . $originalFilename;

                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $renamedFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '-' . $imageMainFile->guessExtension();

                try {
                    $imageMainFile->move($this->getParameter('image_directory'), $newFilename);

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

            }
            $trick->setCreatedAt(new \DateTime());
            $trick->setImageMain($newFilename);
            

            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('homepage');
        }
        
        return $this->render('trick/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}