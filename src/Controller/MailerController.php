<?php
namespace App\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerController extends AbstractController
{
/**
* @Route("/email")
* @param MailerInterface $mailer
* @return Response
*/
    public function sendEmail(MailerInterface $mailer)
    {
        $email = (new Email())
	    ->from('mailtrapqa@exampdle.com')
		->to('newuser@example.com')
        ->subject('Best practices of building HTML emails')
        ->html('Hey! http://www.google.fr Learn the best practices of building HTML emails and play with ready-to-go templates. Mailtrap’s Guide on How to Build HTML Email is live on our blog');
 
        $mailer->send($email);

        return $this->render('email/index.html.twig');
    }
}