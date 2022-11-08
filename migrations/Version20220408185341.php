<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408185341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation_files ADD document_groupe_id INT NOT NULL');
        $this->addSql('ALTER TABLE formation_files ADD CONSTRAINT FK_70BEDE2C687D1D2D FOREIGN KEY (document_groupe_id) REFERENCES document_groupe (id)');
        $this->addSql('CREATE INDEX IDX_70BEDE2C687D1D2D ON formation_files (document_groupe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation_files DROP FOREIGN KEY FK_70BEDE2C687D1D2D');
        $this->addSql('DROP INDEX IDX_70BEDE2C687D1D2D ON formation_files');
        $this->addSql('ALTER TABLE formation_files DROP document_groupe_id');
    }
}
