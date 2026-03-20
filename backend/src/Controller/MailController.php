<?php

namespace App\Controller;

use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    #[Route('/send-mail')]
    public function send(MailService $mailService): Response
    {
        $mailService->send(
            'test@mailhog.local',
            'Test Mail',
            'Hello Mailhog'
        );

        return new Response('sent');
    }
}
