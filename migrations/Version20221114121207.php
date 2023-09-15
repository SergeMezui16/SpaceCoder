<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114121207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD level INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EED60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EED60322AC ON project (role_id)');
        $this->addSql('ALTER TABLE ressource ADD categories JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ressource DROP categories');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EED60322AC');
        $this->addSql('DROP INDEX IDX_2FB3D0EED60322AC ON project');
        $this->addSql('ALTER TABLE project DROP role_id');
        $this->addSql('ALTER TABLE article DROP level');
    }
}
