<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260306120646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property ALTER rent_amount TYPE INT');
        $this->addSql('ALTER TABLE property ALTER deposit TYPE INT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property ALTER rent_amount TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE property ALTER deposit TYPE NUMERIC(10, 2)');
    }
}
