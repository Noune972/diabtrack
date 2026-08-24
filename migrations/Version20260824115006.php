<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260824115006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP CONSTRAINT fk_23a0e6688c5f785');
        $this->addSql('DROP INDEX idx_23a0e6688c5f785');
        $this->addSql('ALTER TABLE article DROP article_category_id');
        $this->addSql('ALTER TABLE article ALTER content TYPE TEXT');
        $this->addSql('ALTER TABLE article ALTER category_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD article_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ALTER content TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE article ALTER category_id DROP NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT fk_23a0e6688c5f785 FOREIGN KEY (article_category_id) REFERENCES article_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_23a0e6688c5f785 ON article (article_category_id)');
    }
}
