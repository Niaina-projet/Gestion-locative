<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260306115211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE property (id UUID NOT NULL, title VARCHAR(150) NOT NULL, type VARCHAR(50) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, surface NUMERIC(8, 2) DEFAULT NULL, rooms INT DEFAULT NULL, rent_amount NUMERIC(10, 2) NOT NULL, deposit NUMERIC(10, 2) DEFAULT NULL, status VARCHAR(20) NOT NULL, description TEXT DEFAULT NULL, photos JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, owner_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_8BF21CDE7E3C61F9 ON property (owner_id)');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP CONSTRAINT FK_8BF21CDE7E3C61F9');
        $this->addSql('DROP TABLE property');
    }
}
