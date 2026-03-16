<?php

namespace App\Tests\Security;

use App\Entity\Data;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecuriteTest extends WebTestCase
{

    public function testMdpHashAvecBcryptEstVerifiable(): void
    {
        $hash = password_hash('monMotDePasse', PASSWORD_BCRYPT);
        $this->assertTrue(password_verify('monMotDePasse', $hash));
    }

    public function testDeuxHashsDuMemeMotDePasseSontDifferents(): void
    {
        $hash1 = password_hash('memeMotDePasse', PASSWORD_BCRYPT);
        $hash2 = password_hash('memeMotDePasse', PASSWORD_BCRYPT);
        $this->assertNotSame($hash1, $hash2);
    }

    public function testHashBcryptCommenceParPrefixeReconnaissable(): void
    {
        $hash = password_hash('test', PASSWORD_BCRYPT);
        $this->assertStringStartsWith('$2y$', $hash);
    }

    public function testMauvaisMotDePasseNePasVerifier(): void
    {
        $hash = password_hash('correct', PASSWORD_BCRYPT);
        $this->assertFalse(password_verify('mauvais', $hash));
    }

    public function testMotDePasseVideNePasVerifier(): void
    {
        $hash = password_hash('correct', PASSWORD_BCRYPT);
        $this->assertFalse(password_verify('', $hash));
    }

    public function testHashEstNonReversible(): void
    {
        $hash = password_hash('secret', PASSWORD_BCRYPT);
        $this->assertNotSame('secret', $hash);
    }


    public function testReponseApiContientContentTypeJsonLd(): void
    {
        $client = static::createClient([], ['HTTP_ACCEPT' => 'application/ld+json']);
        $client->request('GET', '/api/data');

        $contentType = $client->getResponse()->headers->get('content-type');
        $this->assertStringContainsString('application/ld+json', $contentType);
    }

    public function testReponseContientHeaderVaryPourCache(): void
    {
        $client = static::createClient([], ['HTTP_ACCEPT' => 'application/ld+json']);
        $client->request('GET', '/api/data');

        $vary = $client->getResponse()->headers->get('vary');
        $this->assertNotNull($vary, 'Le header Vary doit être présent pour le cache conditionnel');
    }

    public function testRequeteOptionsRetourneHeadersCors(): void
    {
        $client = static::createClient();
        $client->request('OPTIONS', '/api/data', [], [], [
            'HTTP_ORIGIN'                         => 'http://localhost:3000',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD'  => 'POST',
            'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'Content-Type',
        ]);

        $response = $client->getResponse();
        $this->assertTrue(
            $response->headers->has('access-control-allow-origin'),
            'Le header Access-Control-Allow-Origin doit être présent'
        );
    }

    public function testCorsNAutoriseQueLesDomainenConfigures(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/data', [], [], [
            'HTTP_ORIGIN' => 'http://localhost:3000',
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        $allowOrigin = $client->getResponse()->headers->get('access-control-allow-origin');
        if ($allowOrigin !== null) {
            $this->assertMatchesRegularExpression(
                '/^(\*|https?:\/\/[a-zA-Z0-9.\-]+(:\d+)?)$/',
                $allowOrigin
            );
        } else {
            $this->markTestSkipped('Pas de header CORS sur cette route (origine non autorisée)');
        }
    }


    public function testApiDocumentationEstAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testRoutesApiNeRetournentPas500(): void
    {
        $client = static::createClient([], ['HTTP_ACCEPT' => 'application/ld+json']);
        $client->request('GET', '/api/data');
        $status = $client->getResponse()->getStatusCode();
        $this->assertNotSame(500, $status, 'La route /api/data ne doit pas retourner 500');
    }

    public function testRouteInexistanteRetourne404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/route_qui_nexiste_pas');
        $this->assertResponseStatusCodeSame(404);
    }


    public function testInjectionSQLDansDataNePasProvoquer500(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $corps = json_encode([
            'data' => "'; DROP TABLE data; --",
        ]);

        $client->request('POST', '/api/data', [], [], [], $corps);
        $status = $client->getResponse()->getStatusCode();
        $this->assertNotSame(500, $status, 'Une injection SQL ne doit pas provoquer une erreur 500');
    }

    public function testInjectionXSSDansDataNePasProvoquer500(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $corps = json_encode([
            'data' => '<script>alert("xss")</script>',
        ]);

        $client->request('POST', '/api/data', [], [], [], $corps);
        $status = $client->getResponse()->getStatusCode();
        $this->assertNotSame(500, $status, 'Une tentative XSS ne doit pas provoquer une erreur 500');
    }

    public function testCorpsJsonVideRetourne400ou422(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $client->request('POST', '/api/data', [], [], [], '');
        $status = $client->getResponse()->getStatusCode();

        $this->assertContains(
            $status,
            [400, 415, 422],
            'Un corps vide doit retourner 400, 415 ou 422'
        );
    }

    public function testChampDataAvecValeurTropLonguePasErreur500(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $corps = json_encode([
            'data' => str_repeat('a', 1000),
        ]);

        $client->request('POST', '/api/data', [], [], [], $corps);
        $this->assertNotSame(500, $client->getResponse()->getStatusCode());
    }

    public function testPostDataUniqidNePasProvoquerDoublon(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $corps1 = json_encode(['data' => 'test_' . uniqid()]);
        $corps2 = json_encode(['data' => 'test_' . uniqid()]);

        $client->request('POST', '/api/data', [], [], [], $corps1);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/api/data', [], [], [], $corps2);
        $this->assertResponseStatusCodeSame(201);
    }
}
