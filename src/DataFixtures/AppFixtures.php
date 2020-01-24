<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Image;
use App\Entity\Trick;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        $slugify = new Slugify();
        
        for ($i = 1; $i <= 20; $i++) {
            
            $name = $faker->sentence();
            $description = '<p>' . join('</p><p>',  $faker->paragraphs(5)) . '</p>';

            $trick = new Trick();

            $trick->setName($name)
                  ->setDescription($description)
                  ->setCreatedAt(new \DateTime());

                
                for ($j = 1; $j <= rand(2, 5); $j++) {
                    $image = new Image();

                    $image->setFilename($faker->imageUrl())
                          ->setTrick($trick);
                    
                    $manager->persist($image);

                }

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
