<?php

namespace App\Controller;

use App\Service\AmbassadeurMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationController extends AbstractController
{
    #[Route('/api/notifications/send', name: 'notification_send', methods: ['POST'])]
    public function send(
        Request $request,
        AmbassadeurMailer $mailer,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            return $this->json(['error' => 'JSON invalide'], 400);
        }

        $violations = $validator->validate($data, new Assert\Collection([
            'fields' => [
                'nom'     => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
                'prenom'  => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
                'mail'    => [new Assert\NotBlank(), new Assert\Email()],
            ],
            'allowExtraFields' => true,
        ]));

        if (count($violations) > 0) {
            return $this->json(['error' => 'Données invalides'], 422);
        }

        $mailer->notifier(
            $data['nom'],
            $data['prenom'],
            $data['mail'],
            $data['statutEtu'] ?? ''
        );

        return $this->json(['message' => 'Notification envoyée'], 201);
    }
}
