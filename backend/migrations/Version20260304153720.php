<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304153720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY `FK_8F91ABF06760FECA`');
        $this->addSql('DROP INDEX UNIQ_8F91ABF06760FECA ON avis');
        $this->addSql('ALTER TABLE avis CHANGE id_visiteur_id visiteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF07F72333D FOREIGN KEY (visiteur_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F91ABF07F72333D ON avis (visiteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF07F72333D');
        $this->addSql('DROP INDEX UNIQ_8F91ABF07F72333D ON avis');
        $this->addSql('ALTER TABLE avis CHANGE visiteur_id id_visiteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT `FK_8F91ABF06760FECA` FOREIGN KEY (id_visiteur_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F91ABF06760FECA ON avis (id_visiteur_id)');
    }
}
