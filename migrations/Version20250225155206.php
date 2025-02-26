<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225155206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des champs nom, promotion, TD, TP dans la table etudiant';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etudiant ADD nom VARCHAR(255) NOT NULL, ADD promotion VARCHAR(10) NOT NULL, ADD td VARCHAR(8) NOT NULL, ADD tp VARCHAR(8) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etudiant DROP nom, DROP promotion, DROP td, DROP tp');
    }
}
