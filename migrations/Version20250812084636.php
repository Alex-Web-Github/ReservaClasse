<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812084636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session ADD teacher_id INT DEFAULT NULL, ADD parent_code VARCHAR(255) NOT NULL, CHANGE label public_code VARCHAR(255) NOT NULL, CHANGE slot_gap slot_interval INT NOT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D441807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D044D5D441807E1D ON session (teacher_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D441807E1D');
        $this->addSql('DROP INDEX IDX_D044D5D441807E1D ON session');
        $this->addSql('ALTER TABLE session ADD label VARCHAR(255) NOT NULL, DROP teacher_id, DROP public_code, DROP parent_code, CHANGE slot_interval slot_gap INT NOT NULL');
    }
}
