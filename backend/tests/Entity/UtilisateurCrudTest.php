<?php

namespace App\Tests\Entity;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * SCRUM-74 – Conformité requêtes CRUD
 */
class UtilisateurCrudTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private UtilisateurRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->repository = $this->em->getRepository(Utilisateur::class);
        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->rollback();
        parent::tearDown();
    }

    private function buildUtilisateur(array $overrides = []): Utilisateur
    {
        $u = new Utilisateur();
        $u->setNom($overrides['nom']                     ?? 'Dupont');
        $u->setPrenom($overrides['prenom']               ?? 'Jean');
        $u->setEmail($overrides['email']                 ?? 'jean.dupont@test.com');
        $u->setTel($overrides['tel']                     ?? '0601020304');
        $u->setEtablissement($overrides['etablissement'] ?? 'Lycee Hugo');
        $u->setDepartement($overrides['departement']     ?? 'MMI');
        $u->setMdp($overrides['mdp']                     ?? 'MotDePasseTest123!');
        $u->setType($overrides['type']                   ?? 'visiteur');
        $u->setStatutEtu($overrides['statutEtu']         ?? 'lyceen');

        if (isset($overrides['heure_arriver'])) {
            $u->setHeureArriver($overrides['heure_arriver']);
        }

        return $u;
    }

    // ── CREATE ────────────────────────────────────────────────────────────────

    public function testCreateUtilisateurPersistsInDatabase(): void
    {
        $utilisateur = $this->buildUtilisateur();
        $this->em->persist($utilisateur);
        $this->em->flush();

        $this->assertNotNull(
            $utilisateur->getId(),
            'L\'ID doit etre genere apres la persistance.'
        );
    }

    public function testCreateUtilisateurSavesAllFields(): void
    {
        $utilisateur = $this->buildUtilisateur([
            'nom'           => 'Martin',
            'prenom'        => 'Sophie',
            'email'         => 'sophie.martin@iut.fr',
            'tel'           => '0612345678',
            'etablissement' => 'IUT Clermont',
            'departement'   => 'GEA',
            'type'          => 'ambassadeur',
            'statutEtu'     => 'etudiant',
        ]);

        $this->em->persist($utilisateur);
        $this->em->flush();

        $found = $this->repository->find($utilisateur->getId());

        $this->assertNotNull($found);
        $this->assertSame('Martin',               $found->getNom());
        $this->assertSame('Sophie',               $found->getPrenom());
        $this->assertSame('sophie.martin@iut.fr', $found->getEmail());
        $this->assertSame('0612345678',           $found->getTel());
        $this->assertSame('IUT Clermont',         $found->getEtablissement());
        $this->assertSame('GEA',                  $found->getDepartement());
        $this->assertSame('ambassadeur',          $found->getType());
        $this->assertSame('etudiant',             $found->getStatutEtu());
    }

    // ── READ ──────────────────────────────────────────────────────────────────

    public function testReadUtilisateurById(): void
    {
        $utilisateur = $this->buildUtilisateur(['email' => 'read@test.com']);
        $this->em->persist($utilisateur);
        $this->em->flush();

        $this->em->clear();
        $found = $this->repository->find($utilisateur->getId());

        $this->assertNotNull($found, 'L\'utilisateur doit etre retrouvable par son ID.');
        $this->assertSame('read@test.com', $found->getEmail());
    }

    public function testReadUtilisateurByEmail(): void
    {
        $utilisateur = $this->buildUtilisateur(['email' => 'unique@test.com']);
        $this->em->persist($utilisateur);
        $this->em->flush();

        $this->em->clear();
        $found = $this->repository->findOneBy(['email' => 'unique@test.com']);

        $this->assertNotNull($found);
        $this->assertSame('unique@test.com', $found->getEmail());
    }

    public function testReadUtilisateursByType(): void
    {
        $this->em->persist($this->buildUtilisateur(['email' => 'v1@test.com', 'type' => 'visiteur']));
        $this->em->persist($this->buildUtilisateur(['email' => 'v2@test.com', 'type' => 'visiteur']));
        $this->em->persist($this->buildUtilisateur(['email' => 'a1@test.com', 'type' => 'ambassadeur']));
        $this->em->flush();

        $visiteurs = $this->repository->findBy(['type' => 'visiteur']);
        $this->assertGreaterThanOrEqual(2, count($visiteurs));
    }

    public function testReadNonExistentUtilisateurReturnsNull(): void
    {
        // ✅ CORRECTION BUG #1 :
        // L'ID est un entier (auto-increment) – passer une string 'id-inexistant-xyz'
        // faisait lever une exception Doctrine au lieu de retourner null.
        // On passe 999999 (entier impossible en test) pour forcer un vrai null.
        $found = $this->repository->find(999999);
        $this->assertNull($found, 'La recherche d\'un ID inexistant doit retourner null.');
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────

    public function testUpdateUtilisateurEmail(): void
    {
        $utilisateur = $this->buildUtilisateur(['email' => 'before@test.com']);
        $this->em->persist($utilisateur);
        $this->em->flush();

        $utilisateur->setEmail('after@test.com');
        $this->em->flush();

        $this->em->clear();
        $updated = $this->repository->find($utilisateur->getId());

        $this->assertSame('after@test.com', $updated->getEmail());
    }

    public function testUpdateUtilisateurType(): void
    {
        $utilisateur = $this->buildUtilisateur(['type' => 'visiteur']);
        $this->em->persist($utilisateur);
        $this->em->flush();

        $utilisateur->setType('ambassadeur');
        $this->em->flush();

        $this->em->clear();
        $updated = $this->repository->find($utilisateur->getId());

        $this->assertSame('ambassadeur', $updated->getType());
    }

    public function testUpdateMultipleFieldsAtOnce(): void
    {
        $utilisateur = $this->buildUtilisateur();
        $this->em->persist($utilisateur);
        $this->em->flush();

        $utilisateur->setNom('NouveauNom');
        $utilisateur->setPrenom('NouveauPrenom');
        $utilisateur->setTel('0699999999');
        $this->em->flush();

        $this->em->clear();
        $updated = $this->repository->find($utilisateur->getId());

        $this->assertSame('NouveauNom',    $updated->getNom());
        $this->assertSame('NouveauPrenom', $updated->getPrenom());
        $this->assertSame('0699999999',    $updated->getTel());
    }

    // ── DELETE ────────────────────────────────────────────────────────────────

    public function testDeleteUtilisateurRemovesFromDatabase(): void
    {
        $utilisateur = $this->buildUtilisateur(['email' => 'delete@test.com']);
        $this->em->persist($utilisateur);
        $this->em->flush();

        $id = $utilisateur->getId();
        $this->em->remove($utilisateur);
        $this->em->flush();

        $this->em->clear();
        $deleted = $this->repository->find($id);

        $this->assertNull($deleted, 'L\'utilisateur supprime ne doit plus exister en base.');
    }

    public function testDeleteDoesNotAffectOtherRecords(): void
    {
        $u1 = $this->buildUtilisateur(['email' => 'keep@test.com']);
        $u2 = $this->buildUtilisateur(['email' => 'remove@test.com']);
        $this->em->persist($u1);
        $this->em->persist($u2);
        $this->em->flush();

        $this->em->remove($u2);
        $this->em->flush();

        $this->em->clear();

        $this->assertNotNull($this->repository->find($u1->getId()), 'u1 doit etre intact.');
        $this->assertNull($this->repository->find($u2->getId()),    'u2 doit etre supprime.');
    }
}
