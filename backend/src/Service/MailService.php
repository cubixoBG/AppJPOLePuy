<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class MailService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $mailLogger
    ) {}

    public function send(string $to, string $subject, string $content): void
    {
        $email = (new Email())
            ->from('test@example.com')
            ->to($to)
            ->subject($subject)
            ->text($content);

        $this->mailer->send($email);

        $this->mailLogger->info('mail_sent', [
            'to' => $to,
            'subject' => $subject
        ]);
    }
}
