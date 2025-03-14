<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250313123829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE critere (id INT AUTO_INCREMENT NOT NULL, grille_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, note INT NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, INDEX IDX_7F6A8053985C2966 (grille_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, professeur_id INT DEFAULT NULL, matiere_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, date DATETIME NOT NULL, coef INT NOT NULL, statut VARCHAR(20) NOT NULL, statut_groupe VARCHAR(20) NOT NULL, taille_max_groupe INT DEFAULT NULL, INDEX IDX_1323A575BAB22EE9 (professeur_id), INDEX IDX_1323A575F46CD258 (matiere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fiche_grille (id INT AUTO_INCREMENT NOT NULL, grille_id INT DEFAULT NULL, evaluation_id INT DEFAULT NULL, etudiant_id INT DEFAULT NULL, INDEX IDX_624EBB88985C2966 (grille_id), INDEX IDX_624EBB88456C5646 (evaluation_id), INDEX IDX_624EBB88DDEAB1A3 (etudiant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fiche_groupe (id INT AUTO_INCREMENT NOT NULL, groupe_id INT DEFAULT NULL, etudiant_id INT DEFAULT NULL, INDEX IDX_B2A521F67A45358C (groupe_id), INDEX IDX_B2A521F6DDEAB1A3 (etudiant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fiche_matiere (id INT AUTO_INCREMENT NOT NULL, matiere_id INT DEFAULT NULL, professeur_id INT DEFAULT NULL, INDEX IDX_88150C90F46CD258 (matiere_id), INDEX IDX_88150C90BAB22EE9 (professeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grille (id INT AUTO_INCREMENT NOT NULL, professeur_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, INDEX IDX_D452165FBAB22EE9 (professeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL, evaluation_id INT DEFAULT NULL, nom VARCHAR(20) NOT NULL, INDEX IDX_4B98C21456C5646 (evaluation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, etudiant_id INT DEFAULT NULL, evaluation_id INT DEFAULT NULL, note INT NOT NULL, commentaire VARCHAR(255) NOT NULL, INDEX IDX_CFBDFA14DDEAB1A3 (etudiant_id), INDEX IDX_CFBDFA14456C5646 (evaluation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professeur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE critere ADD CONSTRAINT FK_7F6A8053985C2966 FOREIGN KEY (grille_id) REFERENCES grille (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE fiche_grille ADD CONSTRAINT FK_624EBB88985C2966 FOREIGN KEY (grille_id) REFERENCES grille (id)');
        $this->addSql('ALTER TABLE fiche_grille ADD CONSTRAINT FK_624EBB88456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
        $this->addSql('ALTER TABLE fiche_grille ADD CONSTRAINT FK_624EBB88DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE fiche_groupe ADD CONSTRAINT FK_B2A521F67A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE fiche_groupe ADD CONSTRAINT FK_B2A521F6DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE fiche_matiere ADD CONSTRAINT FK_88150C90F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE fiche_matiere ADD CONSTRAINT FK_88150C90BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
        $this->addSql('ALTER TABLE grille ADD CONSTRAINT FK_D452165FBAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
        $this->addSql('ALTER TABLE fiche_cours ADD etudiant_id INT DEFAULT NULL, ADD matiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche_cours ADD CONSTRAINT FK_874A66A3DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE fiche_cours ADD CONSTRAINT FK_874A66A3F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('CREATE INDEX IDX_874A66A3DDEAB1A3 ON fiche_cours (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_874A66A3F46CD258 ON fiche_cours (matiere_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE critere DROP FOREIGN KEY FK_7F6A8053985C2966');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575BAB22EE9');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575F46CD258');
        $this->addSql('ALTER TABLE fiche_grille DROP FOREIGN KEY FK_624EBB88985C2966');
        $this->addSql('ALTER TABLE fiche_grille DROP FOREIGN KEY FK_624EBB88456C5646');
        $this->addSql('ALTER TABLE fiche_grille DROP FOREIGN KEY FK_624EBB88DDEAB1A3');
        $this->addSql('ALTER TABLE fiche_groupe DROP FOREIGN KEY FK_B2A521F67A45358C');
        $this->addSql('ALTER TABLE fiche_groupe DROP FOREIGN KEY FK_B2A521F6DDEAB1A3');
        $this->addSql('ALTER TABLE fiche_matiere DROP FOREIGN KEY FK_88150C90F46CD258');
        $this->addSql('ALTER TABLE fiche_matiere DROP FOREIGN KEY FK_88150C90BAB22EE9');
        $this->addSql('ALTER TABLE grille DROP FOREIGN KEY FK_D452165FBAB22EE9');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21456C5646');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14DDEAB1A3');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14456C5646');
        $this->addSql('DROP TABLE critere');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE fiche_grille');
        $this->addSql('DROP TABLE fiche_groupe');
        $this->addSql('DROP TABLE fiche_matiere');
        $this->addSql('DROP TABLE grille');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE professeur');
        $this->addSql('ALTER TABLE fiche_cours DROP FOREIGN KEY FK_874A66A3DDEAB1A3');
        $this->addSql('ALTER TABLE fiche_cours DROP FOREIGN KEY FK_874A66A3F46CD258');
        $this->addSql('DROP INDEX IDX_874A66A3DDEAB1A3 ON fiche_cours');
        $this->addSql('DROP INDEX IDX_874A66A3F46CD258 ON fiche_cours');
        $this->addSql('ALTER TABLE fiche_cours DROP etudiant_id, DROP matiere_id');
    }
}
