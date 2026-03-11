<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * ✅ CORRECTIONS v3 appliquées :
 *
 * 1. Toutes les routes /api/users, /api/avis, /api/departements, etc. supprimées :
 *    Ces entités n'existent PAS dans la branche TDD du repo.
 *    La seule entité existante est Data → seule route disponible : /api/data
 *
 * 2. corpsDataValide() remplace corpsUserValide() :
 *    Le seul champ disponible est "data" (string, length 255)
 *
 * 3. Mail dupliqué → non applicable (l'entité Data n'a pas de champ mail)
 *
 * ⚠️  Ces tests nécessitent :
 *    - composer require --dev phpunit/phpunit symfony/test-pack
 *    - php bin/console doctrine:database:create --env=test
 *    - php bin/console doctrine:migrations:migrate --env=test --no-interaction
 */
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

    // =========================================================================
    // Codes de statut — GET collections
    // =========================================================================

    public function testGetCollectionDataRetourne200(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/data');
        $this->assertResponseStatusCodeSame(200);
    }

    // =========================================================================
    // Codes de statut — GET ressource inexistante
    // =========================================================================

    public function testGetDataInexistantRetourne404(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/data/99999');
        $this->assertResponseStatusCodeSame(404);
    }

    // =========================================================================
    // Codes de statut — POST
    // =========================================================================

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

    // =========================================================================
    // Content-Type
    // =========================================================================

    public function testGetDataContentTypeEstJsonLd(): void
    {
        $client = $this->clientJsonLd();
        $client->request('GET', '/api/data');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    // =========================================================================
    // Structure JSON-LD
    // =========================================================================

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

    // =========================================================================
    // Réponse POST
    // =========================================================================

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

    // =========================================================================
    // Méthodes interdites
    // =========================================================================

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

    // =========================================================================
    // CORS
    // =========================================================================

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
