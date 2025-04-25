<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250425134208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_note_critere ADD evaluation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche_note_critere ADD CONSTRAINT FK_A25C59FC456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
        $this->addSql('CREATE INDEX IDX_A25C59FC456C5646 ON fiche_note_critere (evaluation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_note_critere DROP FOREIGN KEY FK_A25C59FC456C5646');
        $this->addSql('DROP INDEX IDX_A25C59FC456C5646 ON fiche_note_critere');
        $this->addSql('ALTER TABLE fiche_note_critere DROP evaluation_id');
    }
}
