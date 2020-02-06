<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResettingType;
use App\Form\RegistrationType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $loginName = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'loginName' => $loginName
        ]);
    }

    /**
     * @Route("/logout", name="account_logout")
     */
    public function logout() {}

    /**
     * @Route("/register", name="account_register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $secret = "6LfXKNYUAAAAAOvOl0Zg1Bqg-sB9ZzVls-79uPyi";
            $response = $_POST['g-recaptcha-response'];
            $remoteip = $_SERVER['REMOTE_ADDR'];
            
            $api_url = "https://www.google.com/recaptcha/api/siteverify?secret=" 
                . $secret
                . "&response=" . $response
                . "&remoteip=" . $remoteip ;
            
            $decode = json_decode(file_get_contents($api_url), true);
            
            if ($decode['success'] == true) {
                $passwordHash = $encoder->encodePassword($user, $user->getPasswordHash());
                $user->setPasswordHash($passwordHash);

                $avatarFile = $form->get('avatar')->getData();
                if ($avatarFile) {
                    $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $renamedFilename = $user->getFirstName() . '_' . $user->getLastName() . '_' . $originalFilename;
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $renamedFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '-' . $avatarFile->guessExtension();
                    try {
                        $avatarFile->move($this->getParameter('image_directory'), $newFilename);

                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $user->setAvatar($newFilename);
                }

                $user->setToken($tokenGenerator->generateToken());
                $user->setIsActive(false);
                $user->setPasswordRequestAt(new \Datetime());

                $manager->persist($user);
                $manager->flush();

                $email = new Email();
                $email->from('no-reply@snowtricks.com')
                    ->to($user->getEmail())
                    ->priority(Email::PRIORITY_HIGH)
                    ->subject('Validation de votre compte utilisateur SnowTricks')
                    ->text("Pour valider votre inscription sur le site SnowTricks, cliquez sur le lien suivant https://127.0.0.1:8000/register/{$user->getId()}/{$user->getToken()}");

                $mailer->send($email);
                $this->addFlash(
                    'success', 
                    'Votre demande d\'inscription a bien été enregistré, vérifier votre boite email pour la valider!'
                );

            return $this->redirectToRoute('account_login');
            }
        }   
        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/register/{id}/{token}", name="account_register_confirmation")
     */
    public function registerConfirmation(User $user, $token, EntityManagerInterface $manager)
    {
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestAt()))
        {
            throw new AccessDeniedHttpException();
        }
    
        $user->setToken(null);
        $user->setPasswordRequestAt(null);
        $user->setIsActive(true);

        $manager->persist($user);

        $manager->flush();

        $this->addFlash(
            'success',
            'Votre compte a été activé avec succès !'
        );

        return $this->redirectToRoute('account_login');
        
    }

    /**
     * @Route("/reset_request", name="account_reset_password_request")
     */
    public function resetPasswordRequest(Request $request, EntityManagerInterface $manager, UserRepository $rep, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer )
    {
        $form = $this->createFormBuilder()
        ->add('loginName', TextType::class, [
            'label' => 'login',
            'attr' => [
                'placeholder' => 'Votre login ...'
            ]
        ])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
            $user = $rep->findOneBy(['loginName' => $form->get('loginName')->getData()]);

            if (!$user) {
                $this->addFlash(
                    'danger', 
                    'Cet utilisateur n\'existe pas !'
                );
                return $this->redirectToRoute('homepage');
            }

            $user->setToken($tokenGenerator->generateToken());
            $user->setPasswordRequestAt(new \Datetime());
            
            $manager->flush();
            
            $email = new Email();
            // $emailBody = 'Pour réinitialiser votre mot de passe, cliquez sur le lien suivant';
            $email->from('no-reply@snowtricks.com')
                  ->to($user->getEmail())
                  ->priority(Email::PRIORITY_HIGH)
                  ->subject('Demande de réinitialisation de mot de passe')
                  ->text("Pour réinitialiser votre mot de passe, cliquez sur le lien suivant https://127.0.0.1:8000/reset/{$user->getId()}/{$user->getToken()}");

            $mailer->send($email);

            $this->addFlash(
                'success', 
                'La demande de réinitialisation a été envoyée, surveillez votre boite email !'
            );

            return $this->redirectToRoute('homepage');

        }

        return $this->render('account/resetPasswordRequest.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("reset/{id}/{token}", name="account_reset_password")
     */
    public function reset(User $user, $token, Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestAt()))
        {
            throw new AccessDeniedHttpException();
        }
        
        $form = $this->createForm(ResettingType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $passwordHash = $encoder->encodePassword($user, $user->getPasswordHash());
            $user->setPasswordHash($passwordHash);
            
            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestAt(null);
            
            $manager->persist($user);
            $manager->flush();
            
            $this->addFlash(
                'success', 
                'Le nouveau mot de passe a été enregistré avec succès !'
            );
            return $this->redirectToRoute('account_login');
        }
        
        return $this->render('account/reset.html.twig', [
            'form' => $form->createView()
            ]);
    }

    /**
     * Check the time elapsed since email send.
     */
    private function isRequestInTime(\Datetime $passwordRequestAt = null)
    {
        if ($passwordRequestAt === null)
        {
            return false;        
        }
        
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestAt->getTimestamp();
        $daySeconds = 60 * 10;
        $response = $interval > $daySeconds ? false : $response = true;
        return $response;
    }
}
