<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316084645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495511F1B11A');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB11F1B11A');
        $this->addSql('DROP TABLE slot');
        $this->addSql('DROP INDEX IDX_42C8495511F1B11A ON reservation');
        $this->addSql('ALTER TABLE reservation ADD slot VARCHAR(255) NOT NULL, DROP slot_id_id');
        $this->addSql('DROP INDEX IDX_5A3811FB11F1B11A ON schedule');
        $this->addSql('ALTER TABLE schedule ADD slot VARCHAR(255) NOT NULL, DROP slot_id_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE slot (id INT AUTO_INCREMENT NOT NULL, slot INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reservation ADD slot_id_id INT DEFAULT NULL, DROP slot');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495511F1B11A FOREIGN KEY (slot_id_id) REFERENCES slot (id)');
        $this->addSql('CREATE INDEX IDX_42C8495511F1B11A ON reservation (slot_id_id)');
        $this->addSql('ALTER TABLE schedule ADD slot_id_id INT NOT NULL, DROP slot');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB11F1B11A FOREIGN KEY (slot_id_id) REFERENCES slot (id)');
        $this->addSql('CREATE INDEX IDX_5A3811FB11F1B11A ON schedule (slot_id_id)');
    }
}
