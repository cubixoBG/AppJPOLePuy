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

    /** Corps JSON-LD minimal valide pour créer un Data */
    private function corpsDataValide(): string
    {
        return json_encode([
            'data' => 'valeur_' . uniqid(),
        ]);
    }


    public function testGetCollectionDataRetourne200(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/data');
        $this->assertResponseStatusCodeSame(200);
    }


    public function testGetDataInexistantRetourne404(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/data/99999');
        $this->assertResponseStatusCodeSame(404);
    }


    public function testPostDataValideRetourne201(): void
    {
        $client = $this->clientJsonLd();
        $client->request('POST', '/api/data', [], [], [], $this->corpsDataValide());
        $this->assertResponseStatusCodeSame(201);
    }

    public function testPostDataCorpsVideRetourne422(): void
    {
        $client = $this->clientJsonLd();
        $client->request('POST', '/api/data', [], [], [], '{}');
        $this->assertResponseStatusCodeSame(422);
    }

    public function testPostJsonMalforme400(): void
    {
        $client = $this->clientJsonLd();
        $client->request('POST', '/api/data', [], [], [], '{ceci_nest_pas_du_json}');
        $this->assertResponseStatusCodeSame(400);
    }


    public function testGetDataContentTypeEstJsonLd(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/data');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }


    public function testGetDataContientContextJsonLd(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/data');
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('@context', $data);
        $this->assertArrayHasKey('@id', $data);
        $this->assertArrayHasKey('@type', $data);
    }

    public function testGetDataContientMembre(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/data');
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertIsArray($data['hydra:member']);
    }

    public function testGetDataContientTotalItems(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/data');
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('hydra:totalItems', $data);
        $this->assertIsInt($data['hydra:totalItems']);
    }


    public function testPostDataRenvoieObjetAvecId(): void
    {
        $client = $this->clientJsonLd();
        $client->request('POST', '/api/data', [], [], [], $this->corpsDataValide());
        $reponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('@id', $reponse);
        $this->assertArrayHasKey('id', $reponse);
        $this->assertIsInt($reponse['id']);
    }

    public function testPostDataRenvoieLeChampData(): void
    {
        $client = $this->clientJsonLd();
        $valeur = 'valeur_unique_' . uniqid();
        $client->request('POST', '/api/data', [], [], [], json_encode(['data' => $valeur]));
        $reponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('data', $reponse);
        $this->assertSame($valeur, $reponse['data']);
    }


    public function testPutSurCollectionRetourne405(): void
    {
        $client = $this->clientJsonLd();
        $client->request('PUT', '/api/data');
        $this->assertResponseStatusCodeSame(405);
    }

    public function testDeleteSurCollectionRetourne405(): void
    {
        $client = $this->clientJsonLd();
        $client->request('DELETE', '/api/data');
        $this->assertResponseStatusCodeSame(405);
    }


    public function testRequeteOptionsRetourneHeadersCorsSurData(): void
    {
        $client = static::createClient();
        $client->request('OPTIONS', '/api/data', [], [], [
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
        $client->request('OPTIONS', '/api/data', [], [], [
            'HTTP_ORIGIN'                        => 'http://localhost:3000',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
        ]);

        $allowedMethods = $client->getResponse()->headers->get('access-control-allow-methods');
        $this->assertNotNull($allowedMethods);
    }
}
