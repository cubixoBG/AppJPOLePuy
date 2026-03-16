<?php

namespace App\Controller;

use App\Service\AmbassadeurMailer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationController extends AbstractController
{
    public function __construct(
        private LoggerInterface $notificationLogger
    ) {}

    #[Route('/api/notifications/send', name: 'notification_send', methods: ['POST'])]
    public function send(
        Request $request,
        AmbassadeurMailer $mailer,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (null === $data) {
            $this->notificationLogger->warning('POST /api/notifications/send - JSON invalide reçu');
            return $this->json(['error' => 'JSON invalide'], 400);
        }

        $violations = $validator->validate($data, new Assert\Collection([
            'fields' => [
                'nom'    => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
                'prenom' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
                'mail'   => [new Assert\NotBlank(), new Assert\Email()],
            ],
            'allowExtraFields' => true,
        ]));

        if (count($violations) > 0) {
            $this->notificationLogger->warning('POST /api/notifications/send - Données invalides', [
                'violations' => array_map(fn($v) => $v->getMessage(), iterator_to_array($violations)),
            ]);
            return $this->json(['error' => 'Données invalides'], 422);
        }

        $this->notificationLogger->info('POST /api/notifications/send - Tentative d\'envoi', [
            'nom'           => $data['nom'],
            'prenom'        => $data['prenom'],
            'mail'          => $data['mail'],
            'tel'           => $data['tel'] ?? 'Non renseigné',
            'etablissement' => $data['etablissement'] ?? 'Non renseigné',
        ]);

        try {
            $mailer->notifier(
                $data['nom'],
                $data['prenom'],
                $data['mail'],
                $data['tel'] ?? '',
                $data['etablissement'] ?? ''
            );
        } catch (\Throwable $e) {
            $this->notificationLogger->error('POST /api/notifications/send - Échec envoi email', [
                'error' => $e->getMessage(),
            ]);
            return $this->json(['error' => 'Échec de l\'envoi de l\'email : ' . $e->getMessage()], 500);
        }

        $this->notificationLogger->info('POST /api/notifications/send - Email envoyé avec succès');

        return $this->json(['message' => 'Notification envoyée'], 201);
    }
}
