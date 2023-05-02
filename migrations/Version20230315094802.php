<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315094802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, room_id_id INT NOT NULL, slot_id_id INT NOT NULL, date DATE NOT NULL, INDEX IDX_5A3811FB35F83FFC (room_id_id), INDEX IDX_5A3811FB11F1B11A (slot_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slot (id INT AUTO_INCREMENT NOT NULL, slot INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB35F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB11F1B11A FOREIGN KEY (slot_id_id) REFERENCES slot (id)');
        $this->addSql('ALTER TABLE reservation ADD user_id_id INT NOT NULL, ADD room_id_id INT DEFAULT NULL, ADD slot_id_id INT DEFAULT NULL, ADD reason LONGTEXT NOT NULL, ADD date DATE NOT NULL, ADD status TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849559D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495535F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495511F1B11A FOREIGN KEY (slot_id_id) REFERENCES slot (id)');
        $this->addSql('CREATE INDEX IDX_42C849559D86650F ON reservation (user_id_id)');
        $this->addSql('CREATE INDEX IDX_42C8495535F83FFC ON reservation (room_id_id)');
        $this->addSql('CREATE INDEX IDX_42C8495511F1B11A ON reservation (slot_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495535F83FFC');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495511F1B11A');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB35F83FFC');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB11F1B11A');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE slot');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849559D86650F');
        $this->addSql('DROP INDEX IDX_42C849559D86650F ON reservation');
        $this->addSql('DROP INDEX IDX_42C8495535F83FFC ON reservation');
        $this->addSql('DROP INDEX IDX_42C8495511F1B11A ON reservation');
        $this->addSql('ALTER TABLE reservation DROP user_id_id, DROP room_id_id, DROP slot_id_id, DROP reason, DROP date, DROP status');
    }
}
