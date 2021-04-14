<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210413000729 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, category_id INT NOT NULL, created_at DATETIME NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_404021BFF675F31B (author_id), INDEX IDX_404021BF12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation_user (formation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_DA4C33095200282E (formation_id), INDEX IDX_DA4C3309A76ED395 (user_id), PRIMARY KEY(formation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BF12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE formation_user ADD CONSTRAINT FK_DA4C33095200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_user ADD CONSTRAINT FK_DA4C3309A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercice ADD formation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exercice ADD CONSTRAINT FK_E418C74D5200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('CREATE INDEX IDX_E418C74D5200282E ON exercice (formation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercice DROP FOREIGN KEY FK_E418C74D5200282E');
        $this->addSql('ALTER TABLE formation_user DROP FOREIGN KEY FK_DA4C33095200282E');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE formation_user');
        $this->addSql('DROP INDEX IDX_E418C74D5200282E ON exercice');
        $this->addSql('ALTER TABLE exercice DROP formation_id');
    }
}
