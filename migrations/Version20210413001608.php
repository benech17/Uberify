<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210413001608 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE resolution (id INT AUTO_INCREMENT NOT NULL, exercice_id INT NOT NULL, user_id INT DEFAULT NULL, tentatives INT DEFAULT NULL, last_try_at DATETIME NOT NULL, is_resolved TINYINT(1) DEFAULT NULL, INDEX IDX_FDD30F8A89D40298 (exercice_id), INDEX IDX_FDD30F8AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resolution ADD CONSTRAINT FK_FDD30F8A89D40298 FOREIGN KEY (exercice_id) REFERENCES exercice (id)');
        $this->addSql('ALTER TABLE resolution ADD CONSTRAINT FK_FDD30F8AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE resolution');
    }
}
