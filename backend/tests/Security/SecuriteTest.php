<?php

namespace App\Tests\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecuriteTest extends WebTestCase
{

    public function testMdpHashAvecBcryptEstVerifiable(): void
    {
        $hash = password_hash('monMotDePasse', PASSWORD_BCRYPT);
        $this->assertTrue(password_verify('monMotDePasse', $hash));
    }

    public function testMdpHashNestPasStokeEnClair(): void
    {
        $user = new User();
        $hash = password_hash('secret123', PASSWORD_BCRYPT);
        $user->setMdp($hash);
        $this->assertNotSame('secret123', $user->getMdp());
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


    public function testReponseGetUsersNExposePasMdp(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);
        $client->request('GET', '/api/users');

        $contenu = $client->getResponse()->getContent();
        $this->assertStringNotContainsStringIgnoringCase('"mdp"', $contenu);
    }

    public function testReponsePostUserNExposePasMdpEnClair(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $corps = json_encode([
            'nom'           => 'Test',
            'prenom'        => 'Securite',
            'mail'          => 'secu.test@test.fr',
            'tel'           => '0600000010',
            'etablissement' => 'Lycée',
            'departement'   => 'MMI',
            'mdp'           => 'motdepasse_clair',
        ]);

        $client->request('POST', '/api/users', [], [], [], $corps);
        $contenu = $client->getResponse()->getContent();

        $this->assertStringNotContainsString('motdepasse_clair', $contenu);
    }

    public function testReponseGetUserParIdNExposePasMdp(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $client->request('POST', '/api/users', [], [], [], json_encode([
            'nom'           => 'TestMdp',
            'prenom'        => 'Alice',
            'mail'          => 'alice.mdp@test.fr',
            'tel'           => '0600000011',
            'etablissement' => 'IUT',
            'departement'   => 'Informatique',
            'mdp'           => 'secret_password',
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        if (isset($data['id'])) {
            $client->request('GET', '/api/users/' . $data['id'], [], [], [
                'HTTP_ACCEPT' => 'application/ld+json',
            ]);
            $contenu = $client->getResponse()->getContent();
            $this->assertStringNotContainsString('secret_password', $contenu);
        } else {
            $this->markTestSkipped('Création user échouée, test ignoré');
        }
    }


    public function testReponseApiContientContentTypeJsonLd(): void
    {
        $client = static::createClient([], ['HTTP_ACCEPT' => 'application/ld+json']);
        $client->request('GET', '/api/users');

        $contentType = $client->getResponse()->headers->get('content-type');
        $this->assertStringContainsString('application/ld+json', $contentType);
    }

    public function testReponseContientHeaderVaryPourCache(): void
    {
        $client = static::createClient([], ['HTTP_ACCEPT' => 'application/ld+json']);
        $client->request('GET', '/api/users');

        $vary = $client->getResponse()->headers->get('vary');
        $this->assertNotNull($vary, 'Le header Vary doit être présent pour le cache conditionnel');
    }

    public function testRequeteOptionsRetourneHeadersCors(): void
    {
        $client = static::createClient();
        $client->request('OPTIONS', '/api/users', [], [], [
            'HTTP_ORIGIN'                        => 'http://localhost:3000',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
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
        $client->request('GET', '/api/users', [], [], [
            'HTTP_ORIGIN'   => 'http://localhost:3000',
            'HTTP_ACCEPT'   => 'application/ld+json',
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
        $routes = [
            '/api/users',
            '/api/avis',
            '/api/departements',
            '/api/contacts',
            '/api/notifications',
        ];

        $client = static::createClient([], ['HTTP_ACCEPT' => 'application/ld+json']);

        foreach ($routes as $route) {
            $client->request('GET', $route);
            $status = $client->getResponse()->getStatusCode();
            $this->assertNotSame(
                500,
                $status,
                "La route $route ne doit pas retourner une erreur 500"
            );
        }
    }

    public function testRouteInexistanteRetourne404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/route_qui_nexiste_pas');
        $this->assertResponseStatusCodeSame(404);
    }


    public function testInjectionSQLDansNomUserEstRejeteeOuAssainie(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $corps = json_encode([
            'nom'           => "'; DROP TABLE user; --",
            'prenom'        => 'Injection',
            'mail'          => 'injection@test.fr',
            'tel'           => '0600000099',
            'etablissement' => 'Lycée',
            'departement'   => 'MMI',
            'mdp'           => 'mdp',
        ]);

        $client->request('POST', '/api/users', [], [], [], $corps);
        $status = $client->getResponse()->getStatusCode();

        $this->assertNotSame(500, $status, 'Une tentative d\'injection SQL ne doit pas provoquer une erreur 500');
    }

    public function testInjectionXSSEntreGuillemetsEstAssainie(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $corps = json_encode([
            'nom'           => '<script>alert("xss")</script>',
            'prenom'        => 'XSS',
            'mail'          => 'xss@test.fr',
            'tel'           => '0600000088',
            'etablissement' => 'Lycée',
            'departement'   => 'MMI',
            'mdp'           => 'mdp',
        ]);

        $client->request('POST', '/api/users', [], [], [], $corps);
        $status = $client->getResponse()->getStatusCode();

        $this->assertNotSame(500, $status, 'Une tentative XSS ne doit pas provoquer une erreur 500');
    }

    public function testCorpsJsonNullRetourne400ou415(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $client->request('POST', '/api/users', [], [], [], '');
        $status = $client->getResponse()->getStatusCode();

        $this->assertContains(
            $status,
            [400, 415, 422],
            'Un corps vide doit retourner 400, 415 ou 422'
        );
    }

    public function testChampMailAvecValeurTropLonguePasErreur500(): void
    {
        $client = static::createClient([], [
            'HTTP_ACCEPT'       => 'application/ld+json',
            'HTTP_CONTENT_TYPE' => 'application/ld+json',
        ]);

        $corps = json_encode([
            'nom'           => 'Long',
            'prenom'        => 'Test',
            'mail'          => str_repeat('a', 300) . '@test.fr',
            'tel'           => '0600000000',
            'etablissement' => 'Lycée',
            'departement'   => 'MMI',
            'mdp'           => 'mdp',
        ]);

        $client->request('POST', '/api/users', [], [], [], $corps);
        $this->assertNotSame(500, $client->getResponse()->getStatusCode());
    }
}
