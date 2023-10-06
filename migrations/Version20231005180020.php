<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005180020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE servant_parish_status (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE servant_servant_level (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE servant_servant_post (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE servant_parish ADD status_id INT DEFAULT NULL, ADD initial VARCHAR(255) NOT NULL, DROP status');
        $this->addSql('ALTER TABLE servant_parish ADD CONSTRAINT FK_B3B73A586BF700BD FOREIGN KEY (status_id) REFERENCES servant_parish_status (id)');
        $this->addSql('CREATE INDEX IDX_B3B73A586BF700BD ON servant_parish (status_id)');
        $this->addSql('ALTER TABLE servant_servant ADD post_id INT DEFAULT NULL, ADD level_id INT DEFAULT NULL, DROP post, DROP level');
        $this->addSql('ALTER TABLE servant_servant ADD CONSTRAINT FK_B92FB8E4B89032C FOREIGN KEY (post_id) REFERENCES servant_servant_post (id)');
        $this->addSql('ALTER TABLE servant_servant ADD CONSTRAINT FK_B92FB8E5FB14BA7 FOREIGN KEY (level_id) REFERENCES servant_servant_level (id)');
        $this->addSql('CREATE INDEX IDX_B92FB8E4B89032C ON servant_servant (post_id)');
        $this->addSql('CREATE INDEX IDX_B92FB8E5FB14BA7 ON servant_servant (level_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE servant_parish DROP FOREIGN KEY FK_B3B73A586BF700BD');
        $this->addSql('ALTER TABLE servant_servant DROP FOREIGN KEY FK_B92FB8E5FB14BA7');
        $this->addSql('ALTER TABLE servant_servant DROP FOREIGN KEY FK_B92FB8E4B89032C');
        $this->addSql('DROP TABLE servant_parish_status');
        $this->addSql('DROP TABLE servant_servant_level');
        $this->addSql('DROP TABLE servant_servant_post');
        $this->addSql('DROP INDEX IDX_B92FB8E4B89032C ON servant_servant');
        $this->addSql('DROP INDEX IDX_B92FB8E5FB14BA7 ON servant_servant');
        $this->addSql('ALTER TABLE servant_servant ADD post VARCHAR(255) DEFAULT NULL, ADD level VARCHAR(255) DEFAULT NULL, DROP post_id, DROP level_id');
        $this->addSql('DROP INDEX IDX_B3B73A586BF700BD ON servant_parish');
        $this->addSql('ALTER TABLE servant_parish ADD status VARCHAR(255) DEFAULT \'Paroisse\', DROP status_id, DROP initial');
    }
}
