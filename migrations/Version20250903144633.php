<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250903144633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE eleve_import ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE eleve_import ADD CONSTRAINT FK_4487F4DCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4487F4DCA76ED395 ON eleve_import (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE eleve_import DROP FOREIGN KEY FK_4487F4DCA76ED395');
        $this->addSql('DROP INDEX IDX_4487F4DCA76ED395 ON eleve_import');
        $this->addSql('ALTER TABLE eleve_import DROP user_id');
    }
}
