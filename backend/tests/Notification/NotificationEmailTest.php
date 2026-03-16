<?php

namespace App\Tests\Notification;

use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationEmailTest extends WebTestCase
{
    use MailerAssertionsTrait;

    private function headers(): array
    {
        return [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT'  => 'application/json',
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

    private function logPath(): string
    {
        return dirname(__DIR__, 2) . '/var/log/notification_test.log';
    }

    private function clearLog(): void
    {
        if (file_exists($this->logPath())) {
            file_put_contents($this->logPath(), '');
        }
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

    public function testEnvoiNotificationEmailDestinataireAdresseValide(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload());

        $email = $this->getMailerMessage();
        $to = $email->getTo();
        $this->assertNotEmpty($to);
        $this->assertMatchesRegularExpression('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $to[0]->getAddress());
    }

    public function testEnvoiNotificationEmailContientNomVisiteur(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload([
            'nom'    => 'Martin',
            'prenom' => 'Alice',
            'mail'   => 'alice.martin@example.com',
        ]));

        $email = $this->getMailerMessage();
        $this->assertStringContainsString('Martin', $email->getTextBody());
        $this->assertStringContainsString('Alice', $email->getTextBody());
    }

    public function testEnvoiNotificationEcritDansLog(): void
    {
        $this->clearLog();
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload([
            'nom'    => 'LogTest',
            'prenom' => 'Jean',
            'mail'   => 'logtest@example.com',
        ]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertFileExists($this->logPath());
        $contenu = file_get_contents($this->logPath());
        $this->assertStringContainsString('Notification envoyée', $contenu);
        $this->assertStringContainsString('LogTest', $contenu);
    }

    public function testLogContientMailDuVisiteur(): void
    {
        $this->clearLog();
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload([
            'mail' => 'unique.test@example.com',
        ]));

        $contenu = file_get_contents($this->logPath());
        $this->assertStringContainsString('unique.test@example.com', $contenu);
    }

    public function testLogEcritWarningPourDonneesInvalides(): void
    {
        $this->clearLog();
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), $this->payload([
            'mail' => 'pas-un-email',
        ]));

        $this->assertResponseStatusCodeSame(422);
        $contenu = file_get_contents($this->logPath());
        $this->assertStringContainsString('Données invalides', $contenu);
    }

    public function testLogEcritWarningPourJsonInvalide(): void
    {
        $this->clearLog();
        $client = static::createClient();
        $client->request('POST', '/api/notifications/send', [], [], $this->headers(), '{invalide}');

        $this->assertResponseStatusCodeSame(400);
        $contenu = file_get_contents($this->logPath());
        $this->assertStringContainsString('JSON invalide', $contenu);
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
