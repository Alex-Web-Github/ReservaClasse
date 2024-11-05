<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105123332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservations ADD slot_id INT DEFAULT NULL, ADD available VARCHAR(255) NOT NULL, DROP time_start, DROP time_end, CHANGE user_id_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA23959E5119C FOREIGN KEY (slot_id) REFERENCES slots (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4DA239A76ED395 ON reservations (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4DA23959E5119C ON reservations (slot_id)');
        $this->addSql('ALTER TABLE slots DROP time_end, CHANGE date date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE time_start time_start TIME NOT NULL COMMENT \'(DC2Type:time_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239A76ED395');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA23959E5119C');
        $this->addSql('DROP INDEX UNIQ_4DA239A76ED395 ON reservations');
        $this->addSql('DROP INDEX UNIQ_4DA23959E5119C ON reservations');
        $this->addSql('ALTER TABLE reservations ADD user_id_id INT DEFAULT NULL, ADD time_end VARCHAR(255) NOT NULL, DROP user_id, DROP slot_id, CHANGE available time_start VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE slots ADD time_end VARCHAR(255) NOT NULL, CHANGE date date VARCHAR(255) NOT NULL, CHANGE time_start time_start VARCHAR(255) NOT NULL');
    }
}
