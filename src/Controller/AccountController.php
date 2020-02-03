<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $userEmail = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'userEmail' => $userEmail
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

            $sentEmail = $mailer->send($email);
            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
