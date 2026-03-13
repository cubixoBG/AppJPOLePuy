<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatutUserApiTest extends WebTestCase
{
    private function headers(): array
    {
        return [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT'  => 'application/ld+json',
        ];
    }

    public function testGetUsersContientChampStatut(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users', [], [], $this->headers());
        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);
        if (!empty($data['hydra:member'])) {
            $this->assertArrayHasKey('statut', $data['hydra:member'][0]);
        } else {
            $this->markTestSkipped('Aucun utilisateur en base pour ce test.');
        }
    }

    public function testStatutAccepteValeurDisponible(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'nom'          => 'Test',
            'prenom'       => 'Statut',
            'mail'         => 'test.statut@example.com',
            'tel'          => '0600000000',
            'etablissement'=> 'IUT',
            'departement'  => 'MMI',
            'mdp'          => 'secret',
            'statut'       => 'disponible',
        ]);
        $client->request('POST', '/api/users', [], [], $this->headers(), $payload);
        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('statut', $data);
        $this->assertSame('disponible', $data['statut']);
    }

    public function testStatutAccepteValeurIndisponible(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'nom'          => 'Test',
            'prenom'       => 'Indispo',
            'mail'         => 'test.indispo@example.com',
            'tel'          => '0600000001',
            'etablissement'=> 'IUT',
            'departement'  => 'MMI',
            'mdp'          => 'secret',
            'statut'       => 'indisponible',
        ]);
        $client->request('POST', '/api/users', [], [], $this->headers(), $payload);
        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('indisponible', $data['statut']);
    }

    public function testStatutDefautEstDisponibleViaPOST(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'nom'          => 'Test',
            'prenom'       => 'Defaut',
            'mail'         => 'test.defaut@example.com',
            'tel'          => '0600000002',
            'etablissement'=> 'IUT',
            'departement'  => 'MMI',
            'mdp'          => 'secret',
        ]);
        $client->request('POST', '/api/users', [], [], $this->headers(), $payload);
        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('statut', $data);
        $this->assertSame('disponible', $data['statut']);
    }
}
