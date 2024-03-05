<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class MailerService
{
    private $adminMail;

    /**
     * Constructeur de MailerService
     */
    public function __construct(
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag
    ) {
        $this->adminMail = $parameterBag->get('admin_mail');
    }

    /**
     * Envoie d'un email à un utilisateur
     *
     * @param string $senderEmail L'adresse email de l'expéditeur
     * @param string $toEmail     L'adresse email du destinataire
     * @param string $subject     Le sujet de l'email
     * @param string $content     Le contenu de l'email
     * @return bool
     */
    public function  sendEmail(string $senderEmail, string $toEmail, string $subject, string $content): bool
    {
        try {
            $email = (new Email())
                ->from($this->adminMail)
                ->to($toEmail)
                ->subject($subject)
                ->text($content);

            $this->mailer->send($email);

            // Retourne true si l'envoi est réussi
            return true;
        } catch (\Exception $exception) {
            // false si l'envoie échoue
            return false;
        }
    }
}
