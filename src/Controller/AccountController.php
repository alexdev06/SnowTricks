<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResettingType;
use App\Form\RegistrationType;
use App\Service\RequestInTime;
use App\Repository\UserRepository;
use App\Service\Captcha;
use App\Service\ImageUploadManager;
use App\Service\EmailGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AccountController extends AbstractController
{
    /**
     * Login page
     * 
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
     * Logout action
     * 
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
    }

    /**
     * Registration page
     * 
     * @Route("/register", name="account_register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer, ImageUploadManager $imageUploadManager, EmailGenerator $emailGenerator, Captcha $captcha)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Captcha service
            $decode = $captcha->captchaCheck();
            if ($decode['success'] == true) {
                // Password is crypted an save in database
                $passwordHash = $encoder->encodePassword($user, $user->getPasswordHash());
                $user->setPasswordHash($passwordHash);
                $avatarFile = $form->get('avatarFile')->getData();

                if ($avatarFile) {
                    // ImageUploadManager service which rename and save avatar image
                    $newFilename = $imageUploadManager->imageSave($avatarFile, ImageUploadManager::AVATAR_DIRECTORY, $user);
                    $user->setAvatar($newFilename);
                }

                $user->setToken($tokenGenerator->generateToken());
                // By default, account is not active
                $user->setIsActive(false);
                $user->setTokenRequestAt(new \Datetime());
                $manager->persist($user);
                $manager->flush();
                // Use the service EmailGenerator to send an activation email with a time limited link to activate account. 
                $emailGenerator->createEmail($mailer, $user, EmailGenerator::VALIDATION_ACCOUNT);
                $this->addFlash(
                    'success',
                    'Votre demande d\'inscription a bien été enregistrée. Consultez votre boite email pour la valider!'
                );

                return $this->redirectToRoute('account_login');
            }
        }

        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Account validation action
     * 
     * @Route("/register/{id}/{token}", name="account_register_confirmation")
     */
    public function registerConfirmation(User $user, $token, EntityManagerInterface $manager,  RequestInTime $requestInTime)
    {
        // Check token validity and delay. Delay check use RequestInTime service
        if ($user->getToken() === null || $token !== $user->getToken() || !$requestInTime->isRequestInTime($user->getTokenRequestAt())) {
            throw new AccessDeniedHttpException();
        }
        // Reset token fields
        $user->setToken(null);
        $user->setTokenRequestAt(null);
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
     * Reset account password request form
     * 
     * @Route("/reset_request", name="account_reset_password_request")
     */
    public function resetPasswordRequest(Request $request, EntityManagerInterface $manager, UserRepository $rep, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer, EmailGenerator $emailGenerator)
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
            // Check if user is in database
            if (!$user) {
                $this->addFlash(
                    'danger',
                    'Cet utilisateur n\'existe pas !'
                );

                return $this->redirectToRoute('homepage');
            }

            $user->setToken($tokenGenerator->generateToken());
            $user->setTokenRequestAt(new \Datetime());
            $manager->flush();
            // Send an email with a time limited link to change password form
            $emailGenerator->createEmail($mailer, $user, EmailGenerator::RESET_PASSWORD);
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
     * Reset account password action
     * 
     * @Route("reset/{id}/{token}", name="account_reset_password")
     */
    public function reset(User $user, $token, Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager, RequestInTime $requestInTime)
    {
        // Check token validity and delay. Delay check uses RequestInTime service
        if ($user->getToken() === null || $token !== $user->getToken() || !$requestInTime->isRequestInTime($user->getTokenRequestAt())) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ResettingType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordHash = $encoder->encodePassword($user, $user->getPasswordHash());
            $user->setPasswordHash($passwordHash);
            // Reset token fields
            $user->setToken(null);
            $user->setTokenRequestAt(null);
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
}
