<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260226114026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE edt (id INT AUTO_INCREMENT NOT NULL, qrcode VARCHAR(255) NOT NULL, id_journee_id INT NOT NULL, INDEX IDX_E7A4CB5F6A8CE19F (id_journee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE journee (id INT AUTO_INCREMENT NOT NULL, date TIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, user VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, etablissement VARCHAR(255) NOT NULL, departement VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, heure_arrivee TIME DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, statut_etu VARCHAR(255) DEFAULT NULL, id_journee_id INT DEFAULT NULL, INDEX IDX_8D93D6496A8CE19F (id_journee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE edt ADD CONSTRAINT FK_E7A4CB5F6A8CE19F FOREIGN KEY (id_journee_id) REFERENCES journee (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496A8CE19F FOREIGN KEY (id_journee_id) REFERENCES journee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edt DROP FOREIGN KEY FK_E7A4CB5F6A8CE19F');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496A8CE19F');
        $this->addSql('DROP TABLE edt');
        $this->addSql('DROP TABLE journee');
        $this->addSql('DROP TABLE user');
    }
}
