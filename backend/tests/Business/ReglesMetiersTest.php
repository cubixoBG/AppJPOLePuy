<?php

namespace App\Tests\Business;

use App\Entity\Avis;
use App\Entity\Contact;
use App\Entity\Cour;
use App\Entity\Departement;
use App\Entity\Edt;
use App\Entity\Journee;
use App\Entity\Notification;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ReglesMetiersTest extends TestCase
{

    public function testUserAvecTousLesChampsobligatoiresEstValide(): void
    {
        $user = $this->creerUserValide();

        $this->assertNotNull($user->getNom());
        $this->assertNotNull($user->getPrenom());
        $this->assertNotNull($user->getMail());
        $this->assertNotNull($user->getTel());
        $this->assertNotNull($user->getEtablissement());
        $this->assertNotNull($user->getDepartement());
        $this->assertNotNull($user->getMdp());
    }

    public function testUserNomNePasDepasserLongueurMax(): void
    {
        $user = $this->creerUserValide();
        $user->setNom(str_repeat('a', 255));
        $this->assertSame(255, strlen($user->getNom()));
    }

    public function testUserPrenomNePasDepasserLongueurMax(): void
    {
        $user = $this->creerUserValide();
        $user->setPrenom(str_repeat('a', 255));
        $this->assertSame(255, strlen($user->getPrenom()));
    }

    public function testUserMailFormatValide(): void
    {
        $user = $this->creerUserValide();
        $user->setMail('visiteur@lycee-jv.fr');
        $this->assertMatchesRegularExpression(
            '/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
            $user->getMail()
        );
    }

    public function testUserMailFormatAvecSousdomaine(): void
    {
        $user = $this->creerUserValide();
        $user->setMail('a.b@iut.univ-clermont.fr');
        $this->assertMatchesRegularExpression(
            '/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
            $user->getMail()
        );
    }

    public function testUserDepartementMMIEstAccepte(): void
    {
        $user = $this->creerUserValide();
        $user->setDepartement('MMI');
        $this->assertContains($user->getDepartement(), ['MMI', 'Informatique', 'Chimie']);
    }

    public function testUserDepartementInformatiqueEstAccepte(): void
    {
        $user = $this->creerUserValide();
        $user->setDepartement('Informatique');
        $this->assertContains($user->getDepartement(), ['MMI', 'Informatique', 'Chimie']);
    }

    public function testUserDepartementChimieEstAccepte(): void
    {
        $user = $this->creerUserValide();
        $user->setDepartement('Chimie');
        $this->assertContains($user->getDepartement(), ['MMI', 'Informatique', 'Chimie']);
    }

    public function testUserTypeVisiteurEstAccepte(): void
    {
        $user = $this->creerUserValide();
        $user->setType('visiteur');
        $this->assertContains($user->getType(), ['visiteur', 'ambassadeur', null]);
    }

    public function testUserTypeAmbassadeurEstAccepte(): void
    {
        $user = $this->creerUserValide();
        $user->setType('ambassadeur');
        $this->assertContains($user->getType(), ['visiteur', 'ambassadeur', null]);
    }

    public function testUserTypeNullEstAccepte(): void
    {
        $user = $this->creerUserValide();
        $user->setType(null);
        $this->assertNull($user->getType());
    }

    public function testUserMdpEstStockeCommeHash(): void
    {
        $user = $this->creerUserValide();
        $hash = password_hash('motdepasse', PASSWORD_BCRYPT);
        $user->setMdp($hash);
        $this->assertTrue(password_verify('motdepasse', $user->getMdp()));
    }

    public function testUserMdpHashBcryptEstDifferentDuMotDePasseClair(): void
    {
        $user = $this->creerUserValide();
        $hash = password_hash('secret', PASSWORD_BCRYPT);
        $user->setMdp($hash);
        $this->assertNotSame('secret', $user->getMdp());
    }

    public function testAvisNoteMinimaleEstZero(): void
    {
        $avis = new Avis();
        $avis->setNote(0);
        $this->assertGreaterThanOrEqual(0, $avis->getNote());
    }

    public function testAvisNoteMaximaleEstCinq(): void
    {
        $avis = new Avis();
        $avis->setNote(5);
        $this->assertLessThanOrEqual(5, $avis->getNote());
    }

    public function testAvisNoteEstUnEntier(): void
    {
        $avis = new Avis();
        $avis->setNote(3);
        $this->assertIsInt($avis->getNote());
    }

    public function testAvisNotesDansLaPlageValide(int $note): void
    {
        $avis = new Avis();
        $avis->setNote($note);
        $this->assertGreaterThanOrEqual(0, $avis->getNote());
        $this->assertLessThanOrEqual(5, $avis->getNote());
    }

    public static function fournirNotesValides(): array
    {
        return [[0], [1], [2], [3], [4], [5]];
    }

    public function testAvisCommentaireEstOptionnel(): void
    {
        $avis = new Avis();
        $avis->setNote(4);
        $avis->setDate(new \DateTime());
        $avis->setVisiteur($this->creerUserValide());
        $this->assertNull($avis->getCommentaire());
    }

    public function testAvisDateNePeutPasEtreNull(): void
    {
        $avis = new Avis();
        $date = new \DateTime('today');
        $avis->setDate($date);
        $this->assertInstanceOf(\DateTime::class, $avis->getDate());
    }

    public function testAvisVisiteurEstUnUser(): void
    {
        $user = $this->creerUserValide();
        $avis = new Avis();
        $avis->setVisiteur($user);
        $this->assertInstanceOf(User::class, $avis->getVisiteur());
    }

    public function testAvisUnVisiteurParAvis(): void
    {
        $user1 = $this->creerUserValide('a@test.fr');
        $user2 = $this->creerUserValide('b@test.fr');

        $avis1 = new Avis();
        $avis1->setVisiteur($user1);

        $avis2 = new Avis();
        $avis2->setVisiteur($user2);

        $this->assertNotSame($avis1->getVisiteur(), $avis2->getVisiteur());
    }


    public function testContactTypeEstProfOuEtudiant(): void
    {
        $contact = new Contact();
        foreach (['prof', 'étudiant'] as $type) {
            $contact->setType($type);
            $this->assertContains($contact->getType(), ['prof', 'étudiant']);
        }
    }

    public function testContactDomaineParmiValeursConnues(): void
    {
        $domainesValides = ['Développement web', '3d', 'Communication', 'DevOps'];
        $contact = new Contact();
        foreach ($domainesValides as $domaine) {
            $contact->setDomaine($domaine);
            $this->assertContains($contact->getDomaine(), $domainesValides);
        }
    }

    public function testContactMailEstObligatoire(): void
    {
        $contact = new Contact();
        $contact->setMail('contact@iut.fr');
        $this->assertNotNull($contact->getMail());
    }

    public function testContactNomEstObligatoire(): void
    {
        $contact = new Contact();
        $contact->setNom('Bernard');
        $this->assertNotNull($contact->getNom());
    }


    public function testDepartementNomParmiValeursDomaine(): void
    {
        $nomsDepartements = ['MMI', 'Informatique', 'Chimie'];
        $dept = new Departement();
        foreach ($nomsDepartements as $nom) {
            $dept->setNom($nom);
            $this->assertContains($dept->getNom(), $nomsDepartements);
        }
    }

    public function testDepartementDescriptionEstObligatoire(): void
    {
        $dept = new Departement();
        $dept->setDescription('Formation multimédia');
        $this->assertNotEmpty($dept->getDescription());
    }

    public function testDepartementSansLogoEstValide(): void
    {
        $dept = new Departement();
        $dept->setNom('MMI');
        $dept->setDescription('Multimédia');
        $dept->setLogo(null);
        $this->assertNull($dept->getLogo());
    }

    public function testDepartementSansResponsableEstValide(): void
    {
        $dept = new Departement();
        $dept->setNom('Chimie');
        $dept->setDescription('Chimie industrielle');
        $dept->setNomResponsable(null);
        $this->assertNull($dept->getNomResponsable());
    }


    public function testNotificationMessageEstObligatoire(): void
    {
        $notif = new Notification();
        $notif->setMessage('Visiteur présent en salle A');
        $this->assertNotEmpty($notif->getMessage());
    }

    public function testNotificationTitreEstOptionnel(): void
    {
        $notif = new Notification();
        $notif->setMessage('Alerte');
        $notif->setDateEnvoi(new \DateTime());
        $this->assertNull($notif->getTitre());
    }

    public function testNotificationUserEstObligatoire(): void
    {
        $user = $this->creerUserValide();
        $notif = new Notification();
        $notif->setIdUser($user);
        $this->assertNotNull($notif->getIdUser());
    }

    public function testNotificationDateEnvoiEstUneDateValide(): void
    {
        $notif = new Notification();
        $date = new \DateTime('2026-03-04');
        $notif->setDateEnvoi($date);
        $this->assertInstanceOf(\DateTime::class, $notif->getDateEnvoi());
    }

    public function testNotificationMessageMaxLongueur(): void
    {
        $notif = new Notification();
        $notif->setMessage(str_repeat('x', 255));
        $this->assertSame(255, strlen($notif->getMessage()));
    }


    public function testCourNomEstObligatoire(): void
    {
        $cour = new Cour();
        $cour->setNom('Intégration web');
        $this->assertNotEmpty($cour->getNom());
    }

    public function testCourDureeFormatNhNN(): void
    {
        $cour = new Cour();
        foreach (['1h00', '1h30', '2h00', '2h45', '3h00'] as $duree) {
            $cour->setDuree($duree);
            $this->assertMatchesRegularExpression(
                '/^\d+h\d{2}$/',
                $cour->getDuree(),
                "La durée '$duree' doit correspondre au format NhNN"
            );
        }
    }

    public function testCourAnneeParmiValeurs123(): void
    {
        $cour = new Cour();
        foreach (['1', '2', '3'] as $annee) {
            $cour->setAnnee($annee);
            $this->assertContains($cour->getAnnee(), ['1', '2', '3']);
        }
    }

    public function testCourSansEnseignantEstValide(): void
    {
        $cour = new Cour();
        $cour->setNom('Cours sans prof assigné');
        $cour->setEnseignant(null);
        $this->assertNull($cour->getEnseignant());
    }

    public function testEdtQrcodeEstObligatoire(): void
    {
        $edt = new Edt();
        $edt->setQrcode('QR-2026-MMI-001');
        $this->assertNotEmpty($edt->getQrcode());
    }

    public function testEdtJourneeEstObligatoire(): void
    {
        $journee = new Journee();
        $journee->setDate(new \DateTime());

        $edt = new Edt();
        $edt->setIdJournee($journee);
        $this->assertNotNull($edt->getIdJournee());
    }

    public function testEdtPeutAvoirPlusieursCours(): void
    {
        $journee = new Journee();
        $journee->setDate(new \DateTime());

        $edt = new Edt();
        $edt->setQrcode('QR-001');
        $edt->setIdJournee($journee);

        $cour1 = new Cour();
        $cour1->setNom('Développement web');

        $cour2 = new Cour();
        $cour2->setNom('3D Modélisation');

        $edt->addCours($cour1);
        $edt->addCours($cour2);

        $this->assertCount(2, $edt->getCours());
    }


    public function testJourneeDateEstObligatoire(): void
    {
        $journee = new Journee();
        $journee->setDate(new \DateTime('2026-03-14'));
        $this->assertNotNull($journee->getDate());
    }

    public function testJounreePeutAvoirPlusieursVisiteurs(): void
    {
        $journee = new Journee();
        $journee->setDate(new \DateTime());

        $journee->addIdUser($this->creerUserValide('u1@test.fr'));
        $journee->addIdUser($this->creerUserValide('u2@test.fr'));
        $journee->addIdUser($this->creerUserValide('u3@test.fr'));

        $this->assertCount(3, $journee->getIdUser());
    }

    public function testJourneePeutAvoirPlusieursEdts(): void
    {
        $journee = new Journee();
        $journee->setDate(new \DateTime());

        $edt1 = new Edt();
        $edt1->setQrcode('QR-A');
        $edt1->setIdJournee($journee);

        $edt2 = new Edt();
        $edt2->setQrcode('QR-B');
        $edt2->setIdJournee($journee);

        $journee->addIdEdt($edt1);
        $journee->addIdEdt($edt2);

        $this->assertCount(2, $journee->getIdEdt());
    }


    public function testScenarioInscriptionVisiteurComplet(): void
    {
        $journee = new Journee();
        $journee->setDate(new \DateTime('2026-03-14'));

        $visiteur = new User();
        $visiteur->setNom('Martin');
        $visiteur->setPrenom('Tom');
        $visiteur->setMail('tom.martin@lycee.fr');
        $visiteur->setTel('0600000099');
        $visiteur->setEtablissement('Lycée Jules Vallès');
        $visiteur->setDepartement('MMI');
        $visiteur->setMdp(password_hash('pwd', PASSWORD_BCRYPT));
        $visiteur->setType('visiteur');
        $visiteur->setHeureArrivee(new \DateTime('09:30'));

        $journee->addIdUser($visiteur);

        $this->assertSame('Tom', $visiteur->getPrenom());
        $this->assertSame('visiteur', $visiteur->getType());
        $this->assertCount(1, $journee->getIdUser());
        $this->assertSame($journee, $visiteur->getIdJournee());
    }

    public function testScenarioAvisAmbassadeurSurVisiteur(): void
    {
        $visiteur = $this->creerUserValide('visiteur@test.fr');

        $avis = new Avis();
        $avis->setVisiteur($visiteur);
        $avis->setNote(4);
        $avis->setCommentaire('Très bonne présentation du département MMI.');
        $avis->setDate(new \DateTime('2026-03-14'));

        $this->assertSame(4, $avis->getNote());
        $this->assertSame($visiteur, $avis->getVisiteur());
        $this->assertNotEmpty($avis->getCommentaire());
    }

    public function testScenarioNotificationAmbassadeurPourVisiteurArrive(): void
    {
        $ambassadeur = $this->creerUserValide('ambassadeur@iut.fr');
        $ambassadeur->setType('ambassadeur');

        $notif = new Notification();
        $notif->setIdUser($ambassadeur);
        $notif->setTitre('Visiteur arrivé');
        $notif->setMessage('Un visiteur MMI vous attend en salle 102.');
        $notif->setDateEnvoi(new \DateTime());

        $ambassadeur->addNotifications($notif);

        $this->assertCount(1, $ambassadeur->getNotifications());
        $this->assertSame($ambassadeur, $notif->getIdUser());
        $this->assertSame('ambassadeur', $ambassadeur->getType());
    }


    private function creerUserValide(string $mail = 'test@test.fr'): User
    {
        $user = new User();
        $user->setNom('Nom');
        $user->setPrenom('Prénom');
        $user->setMail($mail);
        $user->setTel('0600000000');
        $user->setEtablissement('Lycée');
        $user->setDepartement('MMI');
        $user->setMdp(password_hash('mdp', PASSWORD_BCRYPT));

        return $user;
    }
}
