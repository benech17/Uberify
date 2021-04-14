<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210412230054 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, exercice_id INT NOT NULL, content LONGTEXT NOT NULL, author VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_9474526C89D40298 (exercice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C89D40298 FOREIGN KEY (exercice_id) REFERENCES exercice (id)');
        $this->addSql('ALTER TABLE exercice ADD CONSTRAINT FK_E418C74D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_E418C74D12469DE2 ON exercice (category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comment');
        $this->addSql('ALTER TABLE exercice DROP FOREIGN KEY FK_E418C74D12469DE2');
        $this->addSql('DROP INDEX IDX_E418C74D12469DE2 ON exercice');
    }
}
