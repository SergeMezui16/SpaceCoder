<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221230105542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role_user_authentication DROP FOREIGN KEY FK_315FC2CD60322AC');
        $this->addSql('ALTER TABLE role_user_authentication DROP FOREIGN KEY FK_315FC2CF57685F4');
        $this->addSql('DROP TABLE role_user_authentication');
        $this->addSql('ALTER TABLE user_authentication ADD role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_authentication ADD CONSTRAINT FK_953116A4D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_953116A4D60322AC ON user_authentication (role_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE role_user_authentication (role_id INT NOT NULL, user_authentication_id INT NOT NULL, INDEX IDX_315FC2CF57685F4 (user_authentication_id), INDEX IDX_315FC2CD60322AC (role_id), PRIMARY KEY(role_id, user_authentication_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE role_user_authentication ADD CONSTRAINT FK_315FC2CD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user_authentication ADD CONSTRAINT FK_315FC2CF57685F4 FOREIGN KEY (user_authentication_id) REFERENCES user_authentication (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_authentication DROP FOREIGN KEY FK_953116A4D60322AC');
        $this->addSql('DROP INDEX IDX_953116A4D60322AC ON user_authentication');
        $this->addSql('ALTER TABLE user_authentication DROP role_id');
    }
}
