<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for ($k = 1; $k <= 10; $k++) {
            $user = new User();
            $firstName = $faker->firstname;
            $lastName = $faker->lastname;
            $email = $faker->email;
            $passwordHash = $this->encoder->encodePassword($user, 'password');

            $male = 'https://randomuser.me/api/portraits/men';
            $female = 'https://randomuser.me/api/portraits/women/';

            $sexe = mt_rand(1,2);
            if ($sexe == 1) {
                $genre = $male;
            } else {
                $genre = $female;
            }

            $person = mt_rand(1,99);

            $avatar = $genre . $person . '.jpg';

            $user->setFirstName($firstName)
                 ->setLastName($lastName)
                 ->setEmail($email)
                 ->setPasswordHash($passwordHash)
                 ->setAvatar($avatar);

            $manager->persist($user);
        }

        for ($h = 1; $h <= 5; $h++) {
            $category = new Category();

            $name = $faker->words(mt_rand(1,3), true);
            $description = '<p>' . join('</p><p>',  $faker->paragraphs(5)) . '</p>';

            $category->setName($name)
                     ->setDescription($description);
            
            for ($i = 1; $i <= 5; $i++) {
                $trick = new Trick();

                $name = $faker->words(mt_rand(1,3), true);
                $description = '<p>' . join('</p><p>',  $faker->paragraphs(3)) . '</p>';
                $imageMain = $faker->imageUrl();
                
                $trick->setName($name)
                ->setDescription($description)
                ->setImageMain($imageMain)
                ->setCategory($category)
                ->setCreatedAt(new \DateTime());
            
                for ($j = 1; $j <= rand(2, 5); $j++) {
                    $image = new Image();
                    
                    $image->setFilename($faker->imageUrl())
                    ->setTrick($trick);
                    
                    $manager->persist($image);
                    
                }

                $manager->persist($trick);
            }

            $manager->persist($category);
        }
        $manager->flush();
    }
}
