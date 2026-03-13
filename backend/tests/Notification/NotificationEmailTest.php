<?php

namespace App\Tests\Notification;

use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationEmailTest extends WebTestCase
{
    use MailerAssertionsTrait;

    private const API_KEY = 'WsFsi9zSPGKIN7aKiOuNgqtiqjuUuIywX-whmThTZLRcND_M';

    private function headers(): array
    {
        return [
            'CONTENT_TYPE'    => 'application/json',
            'HTTP_ACCEPT'     => 'application/json',
            'HTTP_X_API_KEY'  => self::API_KEY,
        ];
    }

    private function payload(array $overrides = []): string
    {
        return json_encode(array_merge([
            'nom'    => 'Dupont',
            'prenom' => 'Jean',
            'mail'   => 'jean.dupont@example.com',
        ], $overrides));
    }

    public function testEnvoiNotificationRetourne201(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload());
        $this->assertResponseStatusCodeSame(201);
    }

    public function testEnvoiNotificationEmailEstEnvoye(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload());
        $this->assertEmailCount(1);
    }

    public function testEnvoiNotificationEmailDestinaireExiste(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload());

        $email = $this->getMailerMessage();
        $to = $email->getTo();
        $this->assertNotEmpty($to);
        $this->assertMatchesRegularExpression(
            '/^[^@]+@[^@]+\.[^@]+$/',
            $to[0]->getAddress()
        );
    }

    public function testEnvoiNotificationEmailContientNomVisiteur(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload([
            'nom' => 'Martin',
            'prenom' => 'Alice',
            'mail' => 'alice.martin@example.com',
        ]));

        $email = $this->getMailerMessage();
        $this->assertStringContainsString('Martin', $email->getTextBody());
        $this->assertStringContainsString('Alice', $email->getTextBody());
    }

    public function testMailVisiteurInvalideRetourne422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload([
            'mail' => 'pas-un-email',
        ]));
        $this->assertResponseStatusCodeSame(422);
    }

    public function testChampManquantRetourne422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), json_encode([
            'nom' => 'Dupont',
        ]));
        $this->assertResponseStatusCodeSame(422);
    }

    public function testCorpsVideRetourne422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), '{}');
        $this->assertResponseStatusCodeSame(422);
    }

    public function testJsonMalformeRetourne400(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), '{invalide}');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testAvecStatutEtuInclusDansEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload([
            'statutEtu' => 'present',
        ]));

        $email = $this->getMailerMessage();
        $this->assertStringContainsString('present', $email->getTextBody());
    }
}
