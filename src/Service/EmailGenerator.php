<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailGenerator
{
    /**
     * Constants which contains email templates paths
     * 
     */
    const RESET_PASSWORD = 'email/resetPassword.html.twig';
    const VALIDATION_ACCOUNT = 'email/validateAccount.html.twig';

    /**
     * Create and send email functionnality
    */
    public function createEmail(MailerInterface $mailer,$user, $template)
    {
        // Subject deducting of the template
        if ($template == self::VALIDATION_ACCOUNT) {
            $subject = 'Activation de votre compte sur SnowTricks';
        } else {
            $subject = 'Modification du mot de passe sur Snowtricks';
        }

        $email = new TemplatedEmail();
        $email->from('no-reply@snowtricks.com')
            ->to($user->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                'user' => $user
            ]);
 
        $mailer->send($email);
    }
}