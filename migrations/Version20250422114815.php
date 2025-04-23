<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250422114815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_note_critere DROP FOREIGN KEY FK_A25C59FC9E5F45AB');
        $this->addSql('ALTER TABLE fiche_note_critere CHANGE critere_id critere_id INT NOT NULL');
        $this->addSql('ALTER TABLE fiche_note_critere ADD CONSTRAINT FK_A25C59FC9E5F45AB FOREIGN KEY (critere_id) REFERENCES critere (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_note_critere DROP FOREIGN KEY FK_A25C59FC9E5F45AB');
        $this->addSql('ALTER TABLE fiche_note_critere CHANGE critere_id critere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche_note_critere ADD CONSTRAINT FK_A25C59FC9E5F45AB FOREIGN KEY (critere_id) REFERENCES critere (id)');
    }
}
