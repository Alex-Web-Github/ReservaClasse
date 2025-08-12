<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812081831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4A76ED395');
        $this->addSql('DROP INDEX IDX_D044D5D4A76ED395 ON session');
        $this->addSql('ALTER TABLE session ADD slot_gap INT NOT NULL, DROP user_id, DROP slot_interval, DROP public_code, DROP parent_code');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session ADD slot_interval INT NOT NULL, ADD public_code VARCHAR(20) NOT NULL, ADD parent_code VARCHAR(20) NOT NULL, CHANGE slot_gap user_id INT NOT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D044D5D4A76ED395 ON session (user_id)');
    }
}
