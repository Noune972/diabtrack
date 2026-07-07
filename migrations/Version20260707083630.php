<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260707083630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment_article ADD article_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment_article ADD CONSTRAINT FK_F1496C767294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_F1496C767294869C ON comment_article (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment_article DROP CONSTRAINT FK_F1496C767294869C');
        $this->addSql('DROP INDEX IDX_F1496C767294869C');
        $this->addSql('ALTER TABLE comment_article DROP article_id');
    }
}
