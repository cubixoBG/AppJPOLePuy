<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class AmbassadeurMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private string $ambassadeurEmail
    ) {}

    public function notifier(string $visiteurNom, string $visiteurPrenom, string $visiteurMail, string $visiteurStatut = ''): void
    {
        $email = (new Email())
            ->from('noreply@jpo-lepuy.fr')
            ->to($this->ambassadeurEmail)
            ->subject('Nouveau visiteur à accompagner - JPO Le Puy')
            ->text(sprintf(
                "Bonjour,\n\nUn nouveau visiteur s'est inscrit et a besoin de votre accompagnement.\n\nNom : %s\nPrénom : %s\nEmail : %s\nStatut : %s\n\nMerci de le prendre en charge.",
                $visiteurNom,
                $visiteurPrenom,
                $visiteurMail,
                $visiteurStatut ?: 'Non renseigné'
            ));

        $this->mailer->send($email);
    }
}
