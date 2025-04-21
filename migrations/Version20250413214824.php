<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413214824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etudiant ADD photo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche_groupe DROP FOREIGN KEY FK_B2A521F6DDEAB1A3');
        $this->addSql('ALTER TABLE fiche_groupe DROP FOREIGN KEY FK_B2A521F67A45358C');
        $this->addSql('ALTER TABLE fiche_groupe CHANGE groupe_id groupe_id INT NOT NULL');
        $this->addSql('ALTER TABLE fiche_groupe ADD CONSTRAINT FK_B2A521F6DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE fiche_groupe ADD CONSTRAINT FK_B2A521F67A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE professeur ADD photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE professeur DROP photo');
        $this->addSql('ALTER TABLE fiche_groupe DROP FOREIGN KEY FK_B2A521F67A45358C');
        $this->addSql('ALTER TABLE fiche_groupe DROP FOREIGN KEY FK_B2A521F6DDEAB1A3');
        $this->addSql('ALTER TABLE fiche_groupe CHANGE groupe_id groupe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche_groupe ADD CONSTRAINT FK_B2A521F67A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE fiche_groupe ADD CONSTRAINT FK_B2A521F6DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE etudiant DROP photo');
    }
}
