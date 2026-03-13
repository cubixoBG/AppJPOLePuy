<?php

namespace App\Tests\Business;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class StatutUserTest extends TestCase
{
    public function testStatutDefautEstDisponible(): void
    {
        $user = new User();
        $this->assertSame('disponible', $user->getStatut());
    }

    public function testSetStatutDisponible(): void
    {
        $user = new User();
        $user->setStatut('disponible');
        $this->assertSame('disponible', $user->getStatut());
    }

    public function testSetStatutIndisponible(): void
    {
        $user = new User();
        $user->setStatut('indisponible');
        $this->assertSame('indisponible', $user->getStatut());
    }

    public function testSetStatutEstFluent(): void
    {
        $user = new User();
        $this->assertSame($user, $user->setStatut('disponible'));
    }

    public function testSetStatutInvalideLeveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $user = new User();
        $user->setStatut('absent');
    }

    public function testStatutVideLeveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $user = new User();
        $user->setStatut('');
    }

    public function testStatutCasseSensibleLeveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $user = new User();
        $user->setStatut('Disponible');
    }

    public function testChangementStatutDisponibleVersIndisponible(): void
    {
        $user = new User();
        $user->setStatut('disponible');
        $user->setStatut('indisponible');
        $this->assertSame('indisponible', $user->getStatut());
    }

    public function testChangementStatutIndisponibleVersDisponible(): void
    {
        $user = new User();
        $user->setStatut('indisponible');
        $user->setStatut('disponible');
        $this->assertSame('disponible', $user->getStatut());
    }

    public function testDeuxUtilisateursStatutsIndependants(): void
    {
        $user1 = new User();
        $user1->setStatut('disponible');

        $user2 = new User();
        $user2->setStatut('indisponible');

        $this->assertSame('disponible', $user1->getStatut());
        $this->assertSame('indisponible', $user2->getStatut());
    }
}
