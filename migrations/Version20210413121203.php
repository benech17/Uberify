<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210413121203 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercice ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE exercice ADD CONSTRAINT FK_E418C74DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E418C74DF675F31B ON exercice (author_id)');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercice DROP FOREIGN KEY FK_E418C74DF675F31B');
        $this->addSql('DROP INDEX IDX_E418C74DF675F31B ON exercice');
        $this->addSql('ALTER TABLE exercice DROP author_id');
        $this->addSql('ALTER TABLE user DROP roles');
    }
}
