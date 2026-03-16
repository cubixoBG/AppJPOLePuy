<?php

namespace App\Tests\Business;

use App\Entity\Data;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ReglesMetiersTest extends TestCase
{

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


    public function testScenarioEnregistrementDonneeJPO(): void
    {
        $data = new Data();
        $data->setData('Visiteur enregistré - MMI - 14/03/2026');

        $this->assertNotNull($data->getData());
        $this->assertStringContainsString('MMI', $data->getData());
        $this->assertNull($data->getId()); // pas encore persisté en BDD
    }
}
