<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319074544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etudiant ADD semestre VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21456C5646');
        $this->addSql('ALTER TABLE groupe ADD taille INT NOT NULL, CHANGE evaluation_id evaluation_id INT NOT NULL, CHANGE nom nom VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE matiere ADD semestre VARCHAR(2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matiere DROP semestre');
        $this->addSql('ALTER TABLE etudiant DROP semestre');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21456C5646');
        $this->addSql('ALTER TABLE groupe DROP taille, CHANGE evaluation_id evaluation_id INT DEFAULT NULL, CHANGE nom nom VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
    }
}
