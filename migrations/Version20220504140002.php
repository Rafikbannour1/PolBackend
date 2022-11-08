<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220504140002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_achat ADD cours_id INT NOT NULL');
        $this->addSql('ALTER TABLE historique_achat ADD CONSTRAINT FK_68295E257ECF78B0 FOREIGN KEY (cours_id) REFERENCES groupe (id)');
        $this->addSql('CREATE INDEX IDX_68295E257ECF78B0 ON historique_achat (cours_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_achat DROP FOREIGN KEY FK_68295E257ECF78B0');
        $this->addSql('DROP INDEX IDX_68295E257ECF78B0 ON historique_achat');
        $this->addSql('ALTER TABLE historique_achat DROP cours_id');
    }
}
