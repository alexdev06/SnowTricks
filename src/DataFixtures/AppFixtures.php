<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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

        $categories = [
            'Grabs' => "Un grab consiste à attraper la planche avec la main pendant le saut. Le verbe anglais to grab signifie « attraper. »
                        Il existe plusieurs types de grabs selon la position de la saisie et la main choisie pour l'effectuer, avec des difficultés variables :
                        mute : saisie de la carre frontside de la planche entre les deux pieds avec la main avant ;
                        sad ou melancholie ou style week : saisie de la carre backside de la planche, entre les deux pieds, avec la main avant ;
                        indy : saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière ;
                        stalefish : saisie de la carre backside de la planche entre les deux pieds avec la main arrière ;
                        tail grab : saisie de la partie arrière de la planche, avec la main arrière ;
                        nose grab : saisie de la partie avant de la planche, avec la main avant ;
                        japan ou japan air : saisie de l'avant de la planche, avec la main avant, du côté de la carre frontside.
                        seat belt: saisie du carre frontside à l'arrière avec la main avant ;
                        truck driver: saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)
                        Un grab est d'autant plus réussi que la saisie est longue. De plus, le saut est d'autant plus esthétique que la saisie du snowboard est franche, ce qui permet au rideur d'accentuer la torsion de son corps grâce à la tension de sa main sur la planche. On dit alors que le grab est tweaké (le verbe anglais to tweak signifie « pincer » mais a également le sens de « peaufiner »).",
            'Rotations' => "On désigne par le mot « rotation » uniquement des rotations horizontales ; les rotations verticales sont des flips. Le principe est d'effectuer une  rotation horizontale pendant le saut, puis d'attérir en position switch ou normal. La nomenclature se base sur le nombre de degrés de rotation effectués :

                        un 180 désigne un demi-tour, soit 180 degrés d'angle ;
                        360, trois six pour un tour complet ;
                        540, cinq quatre pour un tour et demi ;
                        720, sept deux pour deux tours complets ;
                        900 pour deux tours et demi ;
                        1080 ou big foot pour trois tours ;
                        etc.
                        Une rotation peut être frontside ou backside : une rotation frontside correspond à une rotation orientée vers la carre backside. Cela peut paraître incohérent mais l'origine étant que dans un halfpipe ou une rampe de skateboard, une rotation frontside se déclenche naturellement depuis une position frontside (i.e. l'appui se fait sur la carre frontside), et vice-versa. Ainsi pour un rider qui a une position regular (pied gauche devant), une rotation frontside se fait dans le sens inverse des aiguilles d'une montre.

                        Une rotation peut être agrémentée d'un grab, ce qui rend le saut plus esthétique mais aussi plus difficile car la position tweakée a tendance à déséquilibrer le rideur et désaxer la rotation. De plus, le sens de la rotation a tendance à favoriser un sens de grab plutôt qu'un autre. Les rotations de plus de trois tours existent mais sont plus rares, d'abord parce que les modules assez gros pour lancer un tel saut sont rares, et ensuite parce que la vitesse de rotation est tellement élevée qu'un grab devient difficile, ce qui rend le saut considérablement moins esthétique.

                        Pour entrer sur une barre de slide, le rideur peut se mettre perpendiculaire à l'axe de la barre et fera donc un quart de tour en l'air, modulo 360 degrés — il est possible de faire n tours complets plus un quart de tour. On a donc la dénomination suivante pour ce type de rotations :

                        90 pour un quart de tour simple ;
                        270 pour trois quarts de tours ;
                        450 pour un tour un quart ;
                        630 pour un tour trois quarts ;
                        810 pour deux tours un quart ;
                        etc.",
            'Flips' => "Un flip est une rotation verticale. On distingue les front flips, rotations en avant, et les back flips, rotations en arrière.

                        Il est possible de faire plusieurs flips à la suite, et d'ajouter un grab à la rotation.

                        Les flips agrémentés d'une vrille existent aussi (Mac Twist, Hakon Flip, ...), mais de manière beaucoup plus rare, et se confondent souvent avec certaines rotations horizontales désaxées.

                        Néanmoins, en dépit de la difficulté technique relative d'une telle figure, le danger de retomber sur la tête ou la nuque est réel et conduit certaines stations de ski à interdire de telles figures dans ses snowparks.",
            'Rotations désaxées' => "Une rotation désaxée est une rotation initialement horizontale mais lancée avec un mouvement des épaules particulier qui désaxe la rotation. Il existe différents types de rotations désaxées (corkscrew ou cork, rodeo, misty, etc.) en fonction de la manière dont est lancé le buste. Certaines de ces rotations, bien qu'initialement horizontales, font passer la tête en bas.

                        Bien que certaines de ces rotations soient plus faciles à faire sur un certain nombre de tours (ou de demi-tours) que d'autres, il est en théorie possible de d'attérir n'importe quelle rotation désaxée avec n'importe quel nombre de tours, en jouant sur la quantité de désaxage afin de se retrouver à la position verticale au moment voulu.

            Il est également possible d'agrémenter une rotation désaxée par un grab.",
            'Slides' => "Un slide consiste à glisser sur une barre de slide. Le slide se fait soit avec la planche dans l'axe de la barre, soit perpendiculaire, soit plus ou  moins désaxé.

                        On peut slider avec la planche centrée par rapport à la barre (celle-ci se situe approximativement au-dessous des pieds du rideur), mais aussi en nose slide, c'est-à-dire l'avant de la planche sur la barre, ou en tail slide, l'arrière de la planche sur la barre.",
            'One foot tricks' => "Figures réalisée avec un pied décroché de la fixation, afin de tendre la jambe correspondante pour mettre en évidence le fait que le pied n'est pas fixé. Ce type de figure est extrêmement dangereuse pour les ligaments du genou en cas de mauvaise réception.",
            'Old schools' => "Le terme old school désigne un style de freestyle caractérisée par en ensemble de figure et une manière de réaliser des figures passée de mode, qui fait penser au freestyle des années 1980 - début 1990 (par opposition à new school) :

            figures désuètes : Japan air, rocket air, ...
            rotations effectuées avec le corps tendu
            figures saccadées, par opposition au style new school qui privilégie l'amplitude
            À noter que certains tricks old school restent indémodables :

            Backside Air
            Method Air"
        ];
        $tricksList = ['270', 'Air to fakie', 'Back flip', 'Backside air', 'Cork', 'Front flip', 'Handplant', 'Indy', 'Lipslide', 'Mc Twist', 'Mute', 'Sad', 'Stalefish'];
        $tricksDescriptions = [
            "Désigne le degré de rotation, soit 3/4 de tour, fait en entrée ou en sortie sur un jib. Certains riders font également des rotations en 450 degrés avant ou après les jibs.",
            "En pipe, sur un quarter ou un hip, cette figure est un saut sans rotation où le rider retombe dans le sens inverse",
            "Le back flip est une rotation verticale vers l'arrière.",
            "Le grab star du snowboard qui peut être fait d'autant de façon différentes qu'il y a de styles de riders. Il consiste à attraper la carre arrière entre les pieds, ou légèrement devant, et à pousser avec sa jambe arrière pour ramener la planche devant. C'est une figure phare en pipe ou sur un hip en backside. C'est généralement avec ce trick que les riders vont le plus haut.",
            "Le diminutif de corkscrew qui signifie littéralement tire-bouchon et désignait les premières simples rotations têtes en bas en frontside. Désormais, on utilise le mot cork à toute les sauces pour qualifier les figures où le rider passe la tête en bas, peu importe le sens de rotation. Et dorénavant en compétition, on parle souvent de double cork, triple cork et certains riders vont jusqu'au quadruple cork !",
            "Le front flip est une rotation verticale en avant.",
            "Un trick inspiré du skate qui consiste à tenir en équilibre sur une ou deux mains au sommet d'une courbe. Existe avec de nombreuses variantes dans les grabs et les rotations.",
            "Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.",
            "Cette figure consiste à surfer sur une rampe, planche perpendiculaire à la rampe et en avant.",
            "Un grand classique des rotations tête en bas qui se fait en backside, sur un mur backside de pipe. Le Mc Twist est généralement fait en japan, un grab très tweaké (action d'accentuer un grab en se contorsionnant).",
            "Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.",
            "Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.",
            "Saisie de la carre backside de la planche entre les deux pieds avec la main arrière."
        ];
        $tricksImagesMain = [
            "924ensnowcommeenskateunefigureouarticle_normal_image1-5e68a27bea0ea.jpeg",
            "nprattairtofakiejunenm-5e68a1536ecce.jpeg",
            "5498793f4249140638cdfe97c66aa6dd_5e5e2322a2f44_jpeg",
            "codyrosenthal_5e5e2b9792b12_png",
            "924ensnowcommeenskateunefigureouarticle_normal_image1_5e5e2caa3e45f_jpeg",
            "img_7635620x413_5e5e221d8c4ea_jpeg",
            "i-5e689ce1a0f4a.jpeg",
            "weway98epgp01_5e5e1f84e5373_jpeg",
            "noseblunt5db85a7017719-5e68a3fa87479.jpeg",
            "3578243903_c253327f13_b_5e5e2d6bc3949_jpeg",
            "unnamed-5e6903fbd83e8.jpeg",
            "dv0qf5xxuaas1te_5e5e1ecb26769_jpeg",
            "c17b1f74ecb553484b328bc3f3b718e5_5e5e20884380b_jpeg"
        ];
        $videosList = [
            'https://youtu.be/k-CoAquRSwY',
            'https://youtu.be/9sVe_IiXD3A',
            'https://youtu.be/orD3GNRTJAc',
            'https://youtu.be/LSVn5aI56aU'
        ];

        $categoriesList = [];

        foreach ($categories as $name => $description) {
            $category = new Category();
            $category->setName($name)
                ->setDescription($description);

            $manager->persist($category);
            $categoriesList[] = $category;
        }


        $users = [];

        for ($k = 1; $k <= 5; $k++) {
            $user = new User();
            $firstName = $faker->firstNameMale;
            $lastName = $faker->lastname;
            $loginName = $faker->userName;
            $email = $faker->email;
            $passwordHash = $this->encoder->encodePassword($user, 'password');
            $avatar = 'image(' . $k . ').jpeg';

            $user->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setLoginName($loginName)
                ->setPasswordHash($passwordHash)
                ->setIsActive(true)
                ->setAvatar($avatar);

            $manager->persist($user);
            $users[] = $user;
        }

        for ($i = 0; $i <= 12; $i++) {
            $trick = new Trick();
            $name = $tricksList[$i];
            $description = $tricksDescriptions[$i];
            $imageMain = $tricksImagesMain[$i];
            // $categoryNumber = mt_rand(0, 7);

            $trick->setName($name)
                ->setDescription($description)
                ->setImageMain($imageMain)
                ->setCategory($faker->randomElement($categoriesList))
                ->setCreatedAt(new \DateTime())
                ->setUser($faker->randomElement($users));

            for ($j = 1; $j <= rand(1, 3); $j++) {
                $image = new Image();
                $image->setFilename('tricksimage(' . rand(1, 33) . ').jpg')
                    ->setTrick($trick);

                $manager->persist($image);
            }

            for ($l = 1; $l <= mt_rand(10, 20); $l++) {
                $comment = new Comment();
                $comment->setContent($faker->paragraph())
                    ->setUser($faker->randomElement($users))
                    ->setTrick($trick);

                $manager->persist($comment);
            }


            for ($m = 1; $m <= rand(1, 2); $m++) {
                $video = new Video();
                $video->setUrl($videosList[rand(0, 3)])
                    ->setTrick($trick);

                $manager->persist($video);
            }

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
