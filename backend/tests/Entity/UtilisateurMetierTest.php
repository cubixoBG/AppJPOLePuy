<?php

namespace App\Tests\Entity;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * SCRUM-75 – Règles métiers
 */
class UtilisateurMetierTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    private function buildValid(array $overrides = []): Utilisateur
    {
        $u = new Utilisateur();
        $u->setNom($overrides['nom']                     ?? 'Dupont');
        $u->setPrenom($overrides['prenom']               ?? 'Jean');
        $u->setEmail($overrides['email']                 ?? 'jean.dupont@iut.fr');
        $u->setTel($overrides['tel']                     ?? '0601020304');
        $u->setEtablissement($overrides['etablissement'] ?? 'Lycee Victor Hugo');
        $u->setDepartement($overrides['departement']     ?? 'MMI');
        $u->setMdp($overrides['mdp']                     ?? 'SecurePass1!');
        $u->setType($overrides['type']                   ?? 'visiteur');
        $u->setStatutEtu($overrides['statutEtu']         ?? 'lyceen');
        return $u;
    }

    private function countViolations(Utilisateur $u, string $field): int
    {
        return count($this->validator->validateProperty($u, $field));
    }

    // ── TYPES AUTORISES ───────────────────────────────────────────────────────

    public function testTypeVisiteurEstValide(): void
    {
        $u = $this->buildValid(['type' => 'visiteur']);
        $this->assertSame(0, $this->countViolations($u, 'type'));
    }

    public function testTypeAmbassadeurEstValide(): void
    {
        $u = $this->buildValid(['type' => 'ambassadeur']);
        $this->assertSame(0, $this->countViolations($u, 'type'));
    }

    public function testTypeAdminEstValide(): void
    {
        $u = $this->buildValid(['type' => 'admin']);
        $this->assertSame(0, $this->countViolations($u, 'type'));
    }

    public function testTypeInvalideDeclenche_ViolationDeValidation(): void
    {
        $u = $this->buildValid(['type' => 'superuser']);
        $violations = $this->validator->validateProperty($u, 'type');
        $this->assertGreaterThan(0, count($violations), 'Un type non autorise doit etre rejete.');
    }

    public function testTypeVideEstRefuse(): void
    {
        $u = $this->buildValid(['type' => '']);
        $this->assertGreaterThan(0, $this->countViolations($u, 'type'));
    }

    // ── EMAIL ─────────────────────────────────────────────────────────────────

    public function testEmailFormatValide(): void
    {
        $u = $this->buildValid(['email' => 'visiteur@lycee.fr']);
        $this->assertSame(0, $this->countViolations($u, 'email'));
    }

    public function testEmailSansArobaseEstRefuse(): void
    {
        $u = $this->buildValid(['email' => 'pasunmail']);
        $this->assertGreaterThan(0, $this->countViolations($u, 'email'));
    }

    public function testEmailVideEstRefuse(): void
    {
        $u = $this->buildValid(['email' => '']);
        $this->assertGreaterThan(0, $this->countViolations($u, 'email'));
    }

    // ── CHAMPS OBLIGATOIRES ───────────────────────────────────────────────────

    // ✅ CORRECTION BUG #2 :
    // L'annotation @dataProvider était complètement absente.
    // Sans elle, PHPUnit essayait d'appeler testChampsObligatoiresSontRequis()
    // sans argument → Fatal error "Too few arguments".
    // Avec @dataProvider, PHPUnit appelle le test 5 fois (une par champ).

    /**
     * @dataProvider champsObligatoiresProvider
     */
    public function testChampsObligatoiresSontRequis(string $champ): void
    {
        $u = $this->buildValid([$champ => '']);
        $violations = $this->validator->validateProperty($u, $champ);
        $this->assertGreaterThan(
            0,
            count($violations),
            "Le champ $champ ne doit pas etre vide."
        );
    }

    public static function champsObligatoiresProvider(): array
    {
        return [
            'nom'           => ['nom'],
            'prenom'        => ['prenom'],
            'email'         => ['email'],
            'etablissement' => ['etablissement'],
            'type'          => ['type'],
        ];
    }

    // ── TELEPHONE ─────────────────────────────────────────────────────────────

    public function testTelephoneFormatFrancaisValide(): void
    {
        $u = $this->buildValid(['tel' => '0612345678']);
        $this->assertSame(0, $this->countViolations($u, 'tel'));
    }

    public function testTelephoneAvecCaracteresInvalidesEstRefuse(): void
    {
        $u = $this->buildValid(['tel' => 'ABCDEFGHIJ']);
        $this->assertGreaterThan(0, $this->countViolations($u, 'tel'));
    }

    // ── STATUT ETUDIANT ───────────────────────────────────────────────────────

    public function testStatutEtuLyceenEstValide(): void
    {
        $u = $this->buildValid(['statutEtu' => 'lyceen']);
        $this->assertSame(0, $this->countViolations($u, 'statutEtu'));
    }

    public function testStatutEtuEtudiantEstValide(): void
    {
        $u = $this->buildValid(['statutEtu' => 'etudiant']);
        $this->assertSame(0, $this->countViolations($u, 'statutEtu'));
    }

    public function testStatutEtuInvalideEstRefuse(): void
    {
        $u = $this->buildValid(['statutEtu' => 'retraite']);
        $this->assertGreaterThan(0, $this->countViolations($u, 'statutEtu'));
    }

    // ── LOGIQUE METIER ────────────────────────────────────────────────────────

    public function testSeulUnVisiteurPeutSInscrireAUneJourneeImmersion(): void
    {
        $visiteur    = $this->buildValid(['type' => 'visiteur']);
        $ambassadeur = $this->buildValid(['type' => 'ambassadeur']);

        // canInscribeImmersion() est implementee dans l'entite fournie
        $this->assertTrue($visiteur->canInscribeImmersion());
        $this->assertFalse($ambassadeur->canInscribeImmersion());
    }

    public function testUnUtilisateurNePossedePasDeuxTypesSimultanement(): void
    {
        $u = $this->buildValid(['type' => 'visiteur']);
        $this->assertNotSame('ambassadeur', $u->getType());
    }

    // ── MOT DE PASSE ──────────────────────────────────────────────────────────

    public function testMotDePasseVideEstRefuse(): void
    {
        $u = $this->buildValid(['mdp' => '']);
        $this->assertGreaterThan(0, $this->countViolations($u, 'mdp'));
    }

    public function testMotDePasseTropCourtEstRefuse(): void
    {
        $u = $this->buildValid(['mdp' => 'court']);
        $violations = $this->validator->validateProperty($u, 'mdp');
        $this->assertGreaterThan(0, count($violations), 'Un mot de passe trop court doit etre rejete.');
    }

    public function testEntiteValideNeGenerePasDeViolation(): void
    {
        $u = $this->buildValid();
        $violations = $this->validator->validate($u);
        $this->assertCount(0, $violations, 'Un Utilisateur bien renseigne ne doit avoir aucune violation.');
    }
}
