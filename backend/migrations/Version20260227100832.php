<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227100832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, ² INT NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, date DATE NOT NULL, id_visiteur_id INT NOT NULL, UNIQUE INDEX UNIQ_8F91ABF06760FECA (id_visiteur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE cour (id INT AUTO_INCREMENT NOT NULL, duree VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) NOT NULL, annee VARCHAR(255) DEFAULT NULL, enseignant VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE cour_edt (cour_id INT NOT NULL, edt_id INT NOT NULL, INDEX IDX_15CE6659B7942F03 (cour_id), INDEX IDX_15CE6659F814C52E (edt_id), PRIMARY KEY (cour_id, edt_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE departement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, description VARCHAR(255) NOT NULL, nom�_responsable VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, message VARCHAR(255) NOT NULL, date_envoi DATE NOT NULL, id_user_id INT NOT NULL, INDEX IDX_BF5476CA79F37AE5 (id_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF06760FECA FOREIGN KEY (id_visiteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cour_edt ADD CONSTRAINT FK_15CE6659B7942F03 FOREIGN KEY (cour_id) REFERENCES cour (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cour_edt ADD CONSTRAINT FK_15CE6659F814C52E FOREIGN KEY (edt_id) REFERENCES edt (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF06760FECA');
        $this->addSql('ALTER TABLE cour_edt DROP FOREIGN KEY FK_15CE6659B7942F03');
        $this->addSql('ALTER TABLE cour_edt DROP FOREIGN KEY FK_15CE6659F814C52E');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA79F37AE5');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE cour');
        $this->addSql('DROP TABLE cour_edt');
        $this->addSql('DROP TABLE departement');
        $this->addSql('DROP TABLE notification');
    }
}
