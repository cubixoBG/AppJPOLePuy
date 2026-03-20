<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260317061544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, departement_id INT NOT NULL, mail VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, domaine VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, INDEX IDX_4C62E638CCF9E01E (departement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cour (id INT AUTO_INCREMENT NOT NULL, duree VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) NOT NULL, annee VARCHAR(255) DEFAULT NULL, enseignant VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cour_edt (cour_id INT NOT NULL, edt_id INT NOT NULL, INDEX IDX_15CE6659B7942F03 (cour_id), INDEX IDX_15CE6659F814C52E (edt_id), PRIMARY KEY(cour_id, edt_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE departement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, description VARCHAR(255) NOT NULL, nom_responsable VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE edt (id INT AUTO_INCREMENT NOT NULL, id_journee_id INT NOT NULL, qrcode VARCHAR(255) NOT NULL, INDEX IDX_E7A4CB5F6A8CE19F (id_journee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indice (id INT AUTO_INCREMENT NOT NULL, departement_id INT NOT NULL, texte VARCHAR(255) NOT NULL, INDEX IDX_38710B55CCF9E01E (departement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE journee (id INT AUTO_INCREMENT NOT NULL, departement_id INT NOT NULL, date TIME NOT NULL, INDEX IDX_DC179AEDCCF9E01E (departement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, message VARCHAR(255) NOT NULL, date_envoi DATE NOT NULL, INDEX IDX_BF5476CA79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, id_journee_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, etablissement VARCHAR(255) NOT NULL, departement VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, heure_arrivee TIME DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, statut_etu VARCHAR(255) DEFAULT NULL, INDEX IDX_8D93D6496A8CE19F (id_journee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE cour_edt ADD CONSTRAINT FK_15CE6659B7942F03 FOREIGN KEY (cour_id) REFERENCES cour (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cour_edt ADD CONSTRAINT FK_15CE6659F814C52E FOREIGN KEY (edt_id) REFERENCES edt (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE edt ADD CONSTRAINT FK_E7A4CB5F6A8CE19F FOREIGN KEY (id_journee_id) REFERENCES journee (id)');
        $this->addSql('ALTER TABLE indice ADD CONSTRAINT FK_38710B55CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE journee ADD CONSTRAINT FK_DC179AEDCCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496A8CE19F FOREIGN KEY (id_journee_id) REFERENCES journee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638CCF9E01E');
        $this->addSql('ALTER TABLE cour_edt DROP FOREIGN KEY FK_15CE6659B7942F03');
        $this->addSql('ALTER TABLE cour_edt DROP FOREIGN KEY FK_15CE6659F814C52E');
        $this->addSql('ALTER TABLE edt DROP FOREIGN KEY FK_E7A4CB5F6A8CE19F');
        $this->addSql('ALTER TABLE indice DROP FOREIGN KEY FK_38710B55CCF9E01E');
        $this->addSql('ALTER TABLE journee DROP FOREIGN KEY FK_DC179AEDCCF9E01E');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA79F37AE5');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496A8CE19F');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE cour');
        $this->addSql('DROP TABLE cour_edt');
        $this->addSql('DROP TABLE departement');
        $this->addSql('DROP TABLE edt');
        $this->addSql('DROP TABLE indice');
        $this->addSql('DROP TABLE journee');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE user');
    }
}
