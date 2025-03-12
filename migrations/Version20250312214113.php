<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250312214113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_cours ADD etudiant_id INT DEFAULT NULL, ADD matiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche_cours ADD CONSTRAINT FK_874A66A3DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE fiche_cours ADD CONSTRAINT FK_874A66A3F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('CREATE INDEX IDX_874A66A3DDEAB1A3 ON fiche_cours (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_874A66A3F46CD258 ON fiche_cours (matiere_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_cours DROP FOREIGN KEY FK_874A66A3DDEAB1A3');
        $this->addSql('ALTER TABLE fiche_cours DROP FOREIGN KEY FK_874A66A3F46CD258');
        $this->addSql('DROP INDEX IDX_874A66A3DDEAB1A3 ON fiche_cours');
        $this->addSql('DROP INDEX IDX_874A66A3F46CD258 ON fiche_cours');
        $this->addSql('ALTER TABLE fiche_cours DROP etudiant_id, DROP matiere_id');
    }
}
