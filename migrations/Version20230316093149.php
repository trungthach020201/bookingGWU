<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316093149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB35F83FFC');
        $this->addSql('DROP INDEX IDX_5A3811FB35F83FFC ON schedule');
        $this->addSql('ALTER TABLE schedule CHANGE room_id_id room_id INT NOT NULL');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('CREATE INDEX IDX_5A3811FB54177093 ON schedule (room_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB54177093');
        $this->addSql('DROP INDEX IDX_5A3811FB54177093 ON schedule');
        $this->addSql('ALTER TABLE schedule CHANGE room_id room_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB35F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id)');
        $this->addSql('CREATE INDEX IDX_5A3811FB35F83FFC ON schedule (room_id_id)');
    }
}
