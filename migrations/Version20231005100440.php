<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005100440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE servant_diocese (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, bishop VARCHAR(255) DEFAULT \'VACANT\', chaplain VARCHAR(255) DEFAULT \'VACANT\', patron_saint VARCHAR(255) DEFAULT \'Saint Tarcisius de Rome\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE servant_parish (id INT AUTO_INCREMENT NOT NULL, zone_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT \'Paroisse\', location VARCHAR(255) DEFAULT NULL, priest VARCHAR(255) DEFAULT NULL, chaplain VARCHAR(255) DEFAULT \'VACANT\', patron_saint VARCHAR(255) DEFAULT \'Saint Tarcisius de Rome\', description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B3B73A589F2C3FAB (zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE servant_servant (id INT AUTO_INCREMENT NOT NULL, parish_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, bithday DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', sex VARCHAR(255) DEFAULT NULL, post VARCHAR(255) DEFAULT NULL, level VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT \'Novice\', start_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', photo VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B92FB8E8707B11F (parish_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE servant_zone (id INT AUTO_INCREMENT NOT NULL, diocese_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, vicar VARCHAR(255) DEFAULT \'VACANT\', chaplain VARCHAR(255) DEFAULT \'VACANT\', patron_saint VARCHAR(255) DEFAULT \'Saint Tarcisius de Rome\', description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1410DFA0B600009 (diocese_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE servant_parish ADD CONSTRAINT FK_B3B73A589F2C3FAB FOREIGN KEY (zone_id) REFERENCES servant_zone (id)');
        $this->addSql('ALTER TABLE servant_servant ADD CONSTRAINT FK_B92FB8E8707B11F FOREIGN KEY (parish_id) REFERENCES servant_parish (id)');
        $this->addSql('ALTER TABLE servant_zone ADD CONSTRAINT FK_1410DFA0B600009 FOREIGN KEY (diocese_id) REFERENCES servant_diocese (id)');
        $this->addSql('ALTER TABLE article ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP update_at, DROP create_at');
        $this->addSql('ALTER TABLE comment ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP update_at, DROP create_at');
        $this->addSql('ALTER TABLE configuration ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP update_at, DROP create_at');
        $this->addSql('ALTER TABLE contact ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP create_at, DROP update_at');
        $this->addSql('ALTER TABLE notification ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP create_at, DROP update_at');
        $this->addSql('ALTER TABLE project ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP create_at, DROP update_at');
        $this->addSql('ALTER TABLE ressource ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP update_at, DROP create_at');
        $this->addSql('ALTER TABLE role ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP update_at, DROP create_at');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP update_at, DROP create_at');
        $this->addSql('ALTER TABLE user_authentication ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP update_at, DROP create_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE servant_parish DROP FOREIGN KEY FK_B3B73A589F2C3FAB');
        $this->addSql('ALTER TABLE servant_servant DROP FOREIGN KEY FK_B92FB8E8707B11F');
        $this->addSql('ALTER TABLE servant_zone DROP FOREIGN KEY FK_1410DFA0B600009');
        $this->addSql('DROP TABLE servant_diocese');
        $this->addSql('DROP TABLE servant_parish');
        $this->addSql('DROP TABLE servant_servant');
        $this->addSql('DROP TABLE servant_zone');
        $this->addSql('ALTER TABLE user ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE notification ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE article ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE comment ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE contact ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE project ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE role ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE configuration ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE ressource ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE user_authentication ADD update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP created_at, DROP updated_at');
    }
}
