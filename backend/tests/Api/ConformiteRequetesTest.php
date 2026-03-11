<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ConformiteRequetesTest extends WebTestCase
{

    private function clientJsonLd(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        return static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);
    }

    /** Corps JSON-LD minimal valide pour créer un User */
    private function corpsUserValide(): string
    {
        return json_encode([
            'nom'           => 'Dupont',
            'prenom'        => 'Alice',
            'mail'          => 'alice.dupont@test.fr',
            'tel'           => '0600000001',
            'etablissement' => 'Lycée Jules Vallès',
            'departement'   => 'MMI',
            'mdp'           => 'motdepasse',
        ]);
    }

    public function testGetCollectionUsersRetourne200(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCollectionAvisRetourne200(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/avis');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCollectionDepartementsRetourne200(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/departements');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCollectionContactsRetourne200(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/contacts');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCollectionNotificationsRetourne200(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/notifications');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCollectionJourneesRetourne200(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/journees');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCollectionCoursRetourne200(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/cours');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCollectionEdtsRetourne200(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/edts');
        $this->assertResponseStatusCodeSame(200);
    }


    public function testGetUserInexistantRetourne404(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/users/99999');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetAvisInexistantRetourne404(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/avis/99999');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetDepartementInexistantRetourne404(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/departements/99999');
        $this->assertResponseStatusCodeSame(404);
    }


    public function testPostUserValideRetourne201(): void
    {
        $client = $this->clientJsonLd();
        $client->request('POST', '/api/users', [], [], [], $this->corpsUserValide());
        $this->assertResponseStatusCodeSame(201);
    }

    public function testPostDepartementValideRetourne201(): void
    {
        $client = $this->clientJsonLd();
        $corps = json_encode([
            'nom'         => 'MMI',
            'description' => 'Métiers du Multimédia et de l\'Internet',
        ]);
        $client->request('POST', '/api/departements', [], [], [], $corps);
        $this->assertResponseStatusCodeSame(201);
    }


    public function testPostUserCorpsVideRetourne422(): void
    {
        $client = $this->clientJsonLd();
        $client->request('POST', '/api/users', [], [], [], '{}');
        $this->assertResponseStatusCodeSame(422);
    }

    public function testPostAvisCorpsVideRetourne422(): void
    {
        $client = $this->clientJsonLd();
        $client->request('POST', '/api/avis', [], [], [], '{}');
        $this->assertResponseStatusCodeSame(422);
    }


    public function testPostJsonMalforme400(): void
    {
        $client = $this->clientJsonLd();
        $client->request('POST', '/api/users', [], [], [], '{ceci_nest_pas_du_json}');
        $this->assertResponseStatusCodeSame(400);
    }


    public function testGetUsersContentTypeEstJsonLd(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/users');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetAvisContentTypeEstJsonLd(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/avis');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetDepartementsContentTypeEstJsonLd(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/departements');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetContactsContentTypeEstJsonLd(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/contacts');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }


    public function testGetUsersContientContextJsonLd(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/users');
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('@context', $data);
        $this->assertArrayHasKey('@id', $data);
        $this->assertArrayHasKey('@type', $data);
    }

    public function testGetUsersContientMembre(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/users');
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertIsArray($data['hydra:member']);
    }

    public function testGetUsersContientTotalItems(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/users');
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('hydra:totalItems', $data);
        $this->assertIsInt($data['hydra:totalItems']);
    }

    public function testPostUserRenvoieObjetAvecId(): void
    {
        $client = $this->clientJsonLd();
        $client->request('POST', '/api/users', [], [], [], $this->corpsUserValide());
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('@id', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testPostUserRenvoieLesChampsSoumis(): void
    {
        $client = $this->clientJsonLd();
        $client->request('POST', '/api/users', [], [], [], $this->corpsUserValide());
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('Dupont', $data['nom']);
        $this->assertSame('Alice', $data['prenom']);
        $this->assertSame('alice.dupont@test.fr', $data['mail']);
    }


    public function testPutSurCollectionRetourne405(): void
    {
        $client = $this->clientJsonLd();
        $client->request('PUT', '/api/users');
        $this->assertResponseStatusCodeSame(405);
    }

    public function testDeleteSurCollectionRetourne405(): void
    {
        $client = $this->clientJsonLd();
        $client->request('DELETE', '/api/users');
        $this->assertResponseStatusCodeSame(405);
    }


    public function testRequeteOptionsRetourneHeadersCorsSurUsers(): void
    {
        $client = static::createClient();
        $client->request('OPTIONS', '/api/users', [], [], [
            'HTTP_ORIGIN'                        => 'http://localhost:3000',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
        ]);

        $response = $client->getResponse();
        $this->assertTrue(
            $response->headers->has('access-control-allow-origin'),
            'Le header Access-Control-Allow-Origin doit être présent'
        );
    }

    public function testRequeteOptionsExposeLesMethodesAutorisees(): void
    {
        $client = static::createClient();
        $client->request('OPTIONS', '/api/users', [], [], [
            'HTTP_ORIGIN'                        => 'http://localhost:3000',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
        ]);

        $allowedMethods = $client->getResponse()->headers->get('access-control-allow-methods');
        $this->assertNotNull($allowedMethods);
    }
}
