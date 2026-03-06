<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260306090009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE commentaire commentaire VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE cour CHANGE duree duree VARCHAR(255) DEFAULT NULL, CHANGE annee annee VARCHAR(255) DEFAULT NULL, CHANGE enseignant enseignant VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE departement CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE nom_responsable nom_responsable VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE notification CHANGE titre titre VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE heure_arrivee heure_arrivee TIME DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE statut_etu statut_etu VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE departement CHANGE logo logo VARCHAR(255) DEFAULT \'NULL\', CHANGE nom_responsable nom_responsable VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE avis CHANGE commentaire commentaire VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE heure_arrivee heure_arrivee TIME DEFAULT \'NULL\', CHANGE type type VARCHAR(255) DEFAULT \'NULL\', CHANGE statut_etu statut_etu VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE notification CHANGE titre titre VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE cour CHANGE duree duree VARCHAR(255) DEFAULT \'NULL\', CHANGE annee annee VARCHAR(255) DEFAULT \'NULL\', CHANGE enseignant enseignant VARCHAR(255) DEFAULT \'NULL\'');
    }
}
