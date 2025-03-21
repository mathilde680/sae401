<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321100834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fiche_note_critere (id INT AUTO_INCREMENT NOT NULL, etudiant_id INT DEFAULT NULL, critere_id INT DEFAULT NULL, note DOUBLE PRECISION DEFAULT NULL, INDEX IDX_A25C59FCDDEAB1A3 (etudiant_id), INDEX IDX_A25C59FC9E5F45AB (critere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fiche_note_critere ADD CONSTRAINT FK_A25C59FCDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE fiche_note_critere ADD CONSTRAINT FK_A25C59FC9E5F45AB FOREIGN KEY (critere_id) REFERENCES critere (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_note_critere DROP FOREIGN KEY FK_A25C59FCDDEAB1A3');
        $this->addSql('ALTER TABLE fiche_note_critere DROP FOREIGN KEY FK_A25C59FC9E5F45AB');
        $this->addSql('DROP TABLE fiche_note_critere');
    }
}
