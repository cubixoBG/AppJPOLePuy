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

    public function notifier(string $visiteurNom, string $visiteurPrenom, string $visiteurMail, string $visiteurTel = '', string $visiteurEtablissement = ''): void
    {
        $email = (new Email())
            ->from('noreply@jpo-lepuy.fr')
            ->to($this->ambassadeurEmail)
            ->subject('Nouveau visiteur à accompagner - JPO Le Puy')
            ->text(sprintf(
                "Bonjour,\n\nUn nouveau visiteur s'est inscrit et a besoin de votre accompagnement.\n\nNom : %s\nPrénom : %s\nEmail : %s\nTéléphone : %s\nÉtablissement : %s\n\nMerci de le prendre en charge.",
                $visiteurNom,
                $visiteurPrenom,
                $visiteurMail,
                $visiteurTel ?: 'Non renseigné',
                $visiteurEtablissement ?: 'Non renseigné'
            ));

        $this->mailer->send($email);
    }
}
