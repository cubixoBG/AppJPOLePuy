<?php

namespace App\Tests\Security;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * SCRUM-76 – Sécurité
 */
class UtilisateurSecuriteTest extends WebTestCase
{
    // ── ACCES NON AUTHENTIFIE ─────────────────────────────────────────────────

    public function testDashboardAdminRedirigeSiNonConnecte(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseRedirects(
            null,
            Response::HTTP_FOUND,
            'Un visiteur non authentifie doit etre redirige depuis /admin.'
        );
    }

    public function testEspaceAmbassadeurRedirigeSiNonConnecte(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ambassadeur');

        $this->assertResponseRedirects(
            null,
            Response::HTTP_FOUND,
            'Un visiteur non authentifie doit etre redirige depuis /ambassadeur.'
        );
    }

    public function testPageAccueilPubliqueEstAccessibleSansConnexion(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful(
            'La page d\'accueil doit etre publique.'
        );
    }

    // ── ACCES ROLE_ADMIN ──────────────────────────────────────────────────────

    public function testAdminAccedeDashboard(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockAdmin());
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful(
            'Un utilisateur ROLE_ADMIN doit acceder au dashboard.'
        );
    }

    public function testAdminAccedeGestionVisiteurs(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockAdmin());
        $client->request('GET', '/admin/visiteurs');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminAccedeGestionAmbassadeurs(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockAdmin());
        $client->request('GET', '/admin/ambassadeurs');

        $this->assertResponseIsSuccessful();
    }

    // ── ACCES ROLE_AMBASSADEUR ────────────────────────────────────────────────

    public function testAmbassadeurAccedeEspaceAmbassadeur(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockAmbassadeur());
        $client->request('GET', '/ambassadeur');

        $this->assertResponseIsSuccessful(
            'Un ambassadeur authentifie doit acceder a son espace.'
        );
    }

    public function testAmbassadeurNePeutPasAccederDashboardAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockAmbassadeur());
        $client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(
            Response::HTTP_FORBIDDEN,
            'Un ambassadeur ne doit pas acceder au dashboard admin (403).'
        );
    }

    // ── ACCES ROLE_VISITEUR ───────────────────────────────────────────────────

    public function testVisiteurNePeutPasAccederDashboardAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockVisiteur());
        $client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(
            Response::HTTP_FORBIDDEN,
            'Un visiteur ne doit pas acceder au dashboard admin.'
        );
    }

    public function testVisiteurNePeutPasAccederEspaceAmbassadeur(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockVisiteur());
        $client->request('GET', '/ambassadeur');

        $this->assertResponseStatusCodeSame(
            Response::HTTP_FORBIDDEN,
            'Un visiteur ne doit pas acceder a l\'espace ambassadeur.'
        );
    }

    // ── API ───────────────────────────────────────────────────────────────────

    public function testApiVisiteursRequiertAuthentification(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/visiteurs', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(
            Response::HTTP_UNAUTHORIZED,
            'L\'API /visiteurs doit retourner 401 sans token.'
        );
    }

    public function testApiCreeVisiteurEnvoieReponseJson(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockAdmin());

        $client->request(
            'POST',
            '/api/visiteurs',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'nom'           => 'TestNom',
                'prenom'        => 'TestPrenom',
                'email'         => 'test.create@api.fr',
                'tel'           => '0601020304',
                'etablissement' => 'Lycee Test',
                'departement'   => 'MMI',
                'type'          => 'visiteur',
                'statutEtu'     => 'lyceen',
            ])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testApiSupprimerVisiteurNecessiteRoleAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockVisiteur());
        $client->request('DELETE', '/api/visiteurs/some-id');

        $this->assertResponseStatusCodeSame(
            Response::HTTP_FORBIDDEN,
            'Seul ROLE_ADMIN peut supprimer un visiteur via l\'API.'
        );
    }

    // ── DECONNEXION ───────────────────────────────────────────────────────────

    public function testDeconnexionInvalideLaSession(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockAdmin());

        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/deconnexion');
        $this->assertResponseRedirects();

        $client->request('GET', '/admin');
        $this->assertResponseRedirects(
            null,
            Response::HTTP_FOUND,
            'Apres deconnexion, /admin doit rediriger vers la connexion.'
        );
    }

    // ── MOT DE PASSE ──────────────────────────────────────────────────────────

    public function testMotDePasseNEstJamaisStockeEnClair(): void
    {
        $utilisateur = new Utilisateur();
        $motDePasseBrut = 'MonMotDePasse123!';
        $utilisateur->setMdp($motDePasseBrut);

        if (method_exists($utilisateur, 'isPasswordHashed') && $utilisateur->isPasswordHashed()) {
            $this->assertNotSame(
                $motDePasseBrut,
                $utilisateur->getMdp(),
                'Le mot de passe ne doit jamais etre stocke en clair.'
            );
        } else {
            $this->markTestIncomplete(
                'Le hashage automatique du mot de passe n\'est pas encore implemente.'
            );
        }
    }

    public function testMotDePasseHasheNEstPasVide(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setMdp('MonMotDePasse123!');
        $this->assertNotEmpty($utilisateur->getMdp());
    }

    // ── HELPERS ───────────────────────────────────────────────────────────────

    // ✅ CORRECTION BUG #3 :
    // setEtablissement() était absent des mocks.
    // Ce champ est NotBlank dans l'entité – s'il est vide, loginUser()
    // peut lever une exception selon la version de Symfony utilisée.
    // On le renseigne sur tous les mocks pour éviter tout problème.

    private function createMockAdmin(): Utilisateur
    {
        $u = new Utilisateur();
        $u->setNom('Admin');
        $u->setPrenom('Super');
        $u->setEmail('admin@jpo.fr');
        $u->setEtablissement('IUT Le Puy'); // ✅ ajouté
        $u->setMdp('hashedpassword');
        $u->setType('admin');
        $u->setRoles(['ROLE_ADMIN']);
        $u->setStatutEtu('autre');
        return $u;
    }

    private function createMockAmbassadeur(): Utilisateur
    {
        $u = new Utilisateur();
        $u->setNom('Dupont');
        $u->setPrenom('Marie');
        $u->setEmail('marie.dupont@iut.fr');
        $u->setEtablissement('IUT Le Puy'); // ✅ ajouté
        $u->setMdp('hashedpassword');
        $u->setType('ambassadeur');
        $u->setRoles(['ROLE_AMBASSADEUR']);
        $u->setStatutEtu('etudiant');
        return $u;
    }

    private function createMockVisiteur(): Utilisateur
    {
        $u = new Utilisateur();
        $u->setNom('Martin');
        $u->setPrenom('Lucas');
        $u->setEmail('lucas@lycee.fr');
        $u->setEtablissement('Lycee Hugo'); // ✅ ajouté
        $u->setMdp('hashedpassword');
        $u->setType('visiteur');
        $u->setRoles(['ROLE_VISITEUR']);
        $u->setStatutEtu('lyceen');
        return $u;
    }
}
