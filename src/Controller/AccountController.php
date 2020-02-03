<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
        $userLogin = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'userLogin' => $userLogin
        ]);
    }

    /**
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
        
    }

    /**
     * @Route("/register", name="account_register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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


            $manager->persist($user);
            $manager->flush();

            //Confirmation email sending
            $email = new Email();
            $email->from('snowtricks@gmail.com')
                  ->to($user->getEmail())
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('fabien@example.com')
                    //->priority(Email::PRIORITY_HIGH)
                   ->subject('Confirmation de l\'inscription sur SnowTricks')
                   ->text('Bonjour, nous vous confirmons la création de votre compte sur SnowTricks !')
            ;

            $mailer->send($email);
            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset_request", name="account_reset_password_request")
     */
    public function resetPasswordRequest(Request $request, EntityManagerInterface $manager, UserRepository $rep, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer )
    {
        $form = $this->createFormBuilder()
        ->add('loginName', TextType::class, [
            'label' => 'Votre login',
            'attr' => [
                'placeholder' => 'login'
            ]
        ])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $rep->findOneBy(['loginName' => $form->getData()['loginName']]);

            if (!$user) {
                // $request->getSession()->getFlashBag
                return $this->redirectToRoute('homepage');
            }
            $user->setToken($tokenGenerator->generateToken());
            $user->setPasswordRequestAt(new \Datetime());
            $manager->flush();
            
            $email = new Email();
            $email->from('no-reply@gmail.com')
            ->to($user->getEmail())
              //->cc('cc@example.com')
              //->bcc('bcc@example.com')
              //->replyTo('fabien@example.com')
              //->priority(Email::PRIORITY_HIGH)
             ->subject('Demande de réinitialisation de mot de passe')
             ->text('Pour réinitialiser votre mot de passe, cliquez sur le lien suivant');
        }

        return $this->render('account/resetPasswordRequest.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
