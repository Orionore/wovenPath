<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308171854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'update entity chapter : foreign key story id and chapter id ';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapter ADD title VARCHAR(255) NOT NULL, ADD position INT NOT NULL');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52EAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id)');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52E10DCC338 FOREIGN KEY (parent_chapter_id) REFERENCES chapter (id)');
        $this->addSql('CREATE INDEX IDX_F981B52EAA5D4036 ON chapter (story_id)');
        $this->addSql('CREATE INDEX IDX_F981B52E10DCC338 ON chapter (parent_chapter_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52EAA5D4036');
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52E10DCC338');
        $this->addSql('DROP INDEX IDX_F981B52EAA5D4036 ON chapter');
        $this->addSql('DROP INDEX IDX_F981B52E10DCC338 ON chapter');
        $this->addSql('ALTER TABLE chapter DROP title, DROP position');
    }
}
