<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005203541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE servant_diocese ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE servant_parish ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE servant_zone ADD image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE servant_parish DROP image');
        $this->addSql('ALTER TABLE servant_zone DROP image');
        $this->addSql('ALTER TABLE servant_diocese DROP image');
    }
}
