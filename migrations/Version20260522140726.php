<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260522140726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, city VARCHAR(255) NOT NULL, campus_name VARCHAR(255) NOT NULL, address VARCHAR(500) NOT NULL, capacity INT NOT NULL, even_date DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE registration (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, meal_preference VARCHAR(255) NOT NULL, dietary_notes LONGTEXT DEFAULT NULL, ticket_number VARCHAR(255) NOT NULL, registered_at DATETIME NOT NULL, summit_id INT DEFAULT NULL, INDEX IDX_62A8A7A77CE37B3E (summit_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE summit (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, is_active TINYINT NOT NULL, location_id INT DEFAULT NULL, INDEX IDX_6752A31564D218E (location_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A77CE37B3E FOREIGN KEY (summit_id) REFERENCES summit (id)');
        $this->addSql('ALTER TABLE summit ADD CONSTRAINT FK_6752A31564D218E FOREIGN KEY (location_id) REFERENCES location (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A77CE37B3E');
        $this->addSql('ALTER TABLE summit DROP FOREIGN KEY FK_6752A31564D218E');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE registration');
        $this->addSql('DROP TABLE summit');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
