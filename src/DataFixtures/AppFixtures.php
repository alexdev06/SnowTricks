<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Comment;
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

        for ($h = 1; $h <= 4; $h++) {
            $category = new Category();

            $name = $faker->words(mt_rand(1,3), true);
            $description = '<p>' . join('</p><p>',  $faker->paragraphs(5)) . '</p>';

            $category->setName($name)
                     ->setDescription($description);
                     
            for ($k = 1; $k <= 2; $k++) {
                $user = new User();
                $firstName = $faker->firstname;
                $lastName = $faker->lastname;
                $loginName = $faker->userName;
                $email = $faker->email;
                $passwordHash = $this->encoder->encodePassword($user, 'password');

                $male = 'https://randomuser.me/api/portraits/men/';
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
                    ->setLoginName($loginName)
                    ->setPasswordHash($passwordHash)
                    ->setAvatar($avatar)
                    ->setIsActive(true)
                    ;
                    

                    for ($i = 1; $i <= 2; $i++) {
                        $trick = new Trick();
        
                        $name = $faker->words(mt_rand(1,3), true);
                        $description = '<p>' . join('</p><p>',  $faker->paragraphs(3)) . '</p>';
                        $imageMain = $faker->imageUrl();
                        
                        $trick->setName($name)
                        ->setDescription($description)
                        ->setImageMain($imageMain)
                        ->setCategory($category)
                        ->setCreatedAt(new \DateTime())
                        ->setUser($user);
                    
                        for ($j = 1; $j <= rand(2, 5); $j++) {
                            $image = new Image();
                            
                            $image->setFilename($faker->imageUrl())
                            ->setTrick($trick);
                            
                            $manager->persist($image);
                            
                        }
        
                        if (mt_rand(0,1)) {
                            $comment = new Comment();
                            $comment->setContent($faker->paragraph())
                            ->setUser($user)
                            ->setTrick($trick);
    
                            $manager->persist($comment);
                            
                        }
                        $manager->persist($trick);
                    }
                $manager->persist($user);
            }
            $manager->persist($category);
        }
        $manager->flush();
    }
}
