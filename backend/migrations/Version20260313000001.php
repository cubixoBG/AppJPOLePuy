<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260313000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ statut (disponible/indisponible) sur la table user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE user ADD statut VARCHAR(20) NOT NULL DEFAULT 'disponible'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP COLUMN statut');
    }
}
