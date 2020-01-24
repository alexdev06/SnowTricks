<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for ($i = 1; $i <= 20; $i++) {
            
            $name = $faker->words(mt_rand(1,3), true);
            $description = '<p>' . join('</p><p>',  $faker->paragraphs(5)) . '</p>';
            $imageMain = $faker->imageUrl();
            $trick = new Trick();

            $trick->setName($name)
                  ->setDescription($description)
                  ->setImageMain($imageMain)
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
