<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405125513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_groupe ADD groupe_id INT NOT NULL');
        $this->addSql('ALTER TABLE document_groupe ADD CONSTRAINT FK_BADB3D477A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('CREATE INDEX IDX_BADB3D477A45358C ON document_groupe (groupe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_groupe DROP FOREIGN KEY FK_BADB3D477A45358C');
        $this->addSql('DROP INDEX IDX_BADB3D477A45358C ON document_groupe');
        $this->addSql('ALTER TABLE document_groupe DROP groupe_id');
    }
}
