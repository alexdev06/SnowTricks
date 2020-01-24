<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
}
