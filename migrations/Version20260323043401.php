<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260323043401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE software_version (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, system_version VARCHAR(255) NOT NULL, system_version_alt VARCHAR(255) NOT NULL, link VARCHAR(1024) DEFAULT NULL, st VARCHAR(1024) DEFAULT NULL, gd VARCHAR(1024) DEFAULT NULL, is_latest TINYINT NOT NULL, latest_display_version VARCHAR(50) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE software_version');
    }
}
