<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241003204515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE sell_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE sell (id INT NOT NULL, user_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, article_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9B9ED07DA76ED395 ON sell (user_id)');
        $this->addSql('CREATE INDEX IDX_9B9ED07D7E3C61F9 ON sell (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9B9ED07D7294869C ON sell (article_id)');
        $this->addSql('ALTER TABLE sell ADD CONSTRAINT FK_9B9ED07DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sell ADD CONSTRAINT FK_9B9ED07D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sell ADD CONSTRAINT FK_9B9ED07D7294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE sell_id_seq CASCADE');
        $this->addSql('ALTER TABLE sell DROP CONSTRAINT FK_9B9ED07DA76ED395');
        $this->addSql('ALTER TABLE sell DROP CONSTRAINT FK_9B9ED07D7E3C61F9');
        $this->addSql('ALTER TABLE sell DROP CONSTRAINT FK_9B9ED07D7294869C');
        $this->addSql('DROP TABLE sell');
    }
}
