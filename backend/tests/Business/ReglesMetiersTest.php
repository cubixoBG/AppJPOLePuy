<?php

namespace App\Tests\Business;

use App\Entity\Data;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * ✅ CORRECTIONS v3 appliquées :
 *
 * 1. Toutes les entités inexistantes dans la branche TDD supprimées :
 *    User, Avis, Contact, Departement, Edt, Journee, Notification, Cour
 *    → La branche TDD ne contient qu'une seule entité : Data (id + data)
 *
 * 2. Import DataProvider ajouté (Bug #2 corrigé) :
 *    use PHPUnit\Framework\Attributes\DataProvider;
 *    Sans cet import, #[DataProvider] était résolu comme App\Tests\Business\DataProvider
 *    → classe inexistante → le DataProvider était silencieusement ignoré
 *    → PHPUnit tentait de lancer testDataValeurDansLaPlage(string $val) sans argument
 *    → Fatal error : "Too few arguments"
 *
 * 3. Tests réécrits sur la vraie entité Data :
 *    - getData() / setData()
 *    - getId() (non settable, géré par Doctrine)
 *    - Valeur string obligatoire (non null après set)
 *    - Longueur max 255 (conforme au schema Doctrine)
 *    - DataProvider sur plusieurs valeurs valides
 */
class ReglesMetiersTest extends TestCase
{
    // =========================================================================
    // Entité Data — tests unitaires purs (sans BDD)
    // =========================================================================

    public function testDataNouvelleInstanceAIdNull(): void
    {
        $data = new Data();
        $this->assertNull($data->getId());
    }

    public function testDataNouvelleInstanceADataNull(): void
    {
        $data = new Data();
        $this->assertNull($data->getData());
    }

    public function testDataSetDataRetourneInstance(): void
    {
        $data = new Data();
        $result = $data->setData('valeur test');
        $this->assertInstanceOf(Data::class, $result);
    }

    public function testDataSetDataEstFluent(): void
    {
        $data = new Data();
        // setData retourne static → permet le chaînage
        $this->assertSame($data, $data->setData('chaine'));
    }

    public function testDataGetDataRetourneValeurDefinie(): void
    {
        $data = new Data();
        $data->setData('Bonjour IUT Le Puy');
        $this->assertSame('Bonjour IUT Le Puy', $data->getData());
    }

    public function testDataGetDataEstNonNullApresSet(): void
    {
        $data = new Data();
        $data->setData('quelque chose');
        $this->assertNotNull($data->getData());
    }

    public function testDataValeurVideeEstAcceptee(): void
    {
        $data = new Data();
        $data->setData('');
        $this->assertSame('', $data->getData());
    }

    public function testDataValeurMaxLongueur255(): void
    {
        $data = new Data();
        $data->setData(str_repeat('x', 255));
        $this->assertSame(255, strlen($data->getData()));
    }

    public function testDataValeurLongueurNePasDepasserSchemaDoctrineColumn(): void
    {
        $data = new Data();
        $valeur = str_repeat('a', 255);
        $data->setData($valeur);
        // Doctrine Column(length: 255) → vérification applicative
        $this->assertLessThanOrEqual(255, strlen($data->getData()));
    }

    public function testDataValeurPeutContientCaracteresSpeciaux(): void
    {
        $data = new Data();
        $data->setData('éàü@#$%&*()');
        $this->assertSame('éàü@#$%&*()', $data->getData());
    }

    public function testDataValeurPeutContientEspacesEtSauts(): void
    {
        $data = new Data();
        $data->setData("ligne1\nligne2");
        $this->assertStringContainsString("\n", $data->getData());
    }

    public function testDataValeurPeutContientJsonString(): void
    {
        $data = new Data();
        $json = '{"key":"value","number":42}';
        $data->setData($json);
        $this->assertSame($json, $data->getData());
    }

    public function testDataDeuxInstancesSontIndependantes(): void
    {
        $data1 = new Data();
        $data1->setData('valeur A');

        $data2 = new Data();
        $data2->setData('valeur B');

        $this->assertNotSame($data1->getData(), $data2->getData());
        $this->assertSame('valeur A', $data1->getData());
        $this->assertSame('valeur B', $data2->getData());
    }

    public function testDataModificationEcraseLAncienneValeur(): void
    {
        $data = new Data();
        $data->setData('premiere valeur');
        $data->setData('deuxieme valeur');
        $this->assertSame('deuxieme valeur', $data->getData());
    }

    // =========================================================================
    // DataProvider — ✅ Bug #2 corrigé : import use ajouté
    // =========================================================================

    #[DataProvider('fournirValeursValides')]
    public function testDataValeurDansLaPlage(string $valeur): void
    {
        $data = new Data();
        $data->setData($valeur);
        $this->assertNotNull($data->getData());
        $this->assertLessThanOrEqual(255, strlen($data->getData()));
    }

    public static function fournirValeursValides(): array
    {
        return [
            ['MMI'],
            ['Informatique'],
            ['Chimie'],
            ['Journée Portes Ouvertes 2026'],
            [''],
            [str_repeat('z', 100)],
        ];
    }

    // =========================================================================
    // Scénario métier : enregistrement d'une donnée JPO
    // =========================================================================

    public function testScenarioEnregistrementDonneeJPO(): void
    {
        $data = new Data();
        $data->setData('Visiteur enregistré - MMI - 14/03/2026');

        $this->assertNotNull($data->getData());
        $this->assertStringContainsString('MMI', $data->getData());
        $this->assertNull($data->getId()); // pas encore persisté en BDD
    }
}
