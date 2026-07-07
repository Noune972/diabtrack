<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260707075422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE insuline ADD patient_id INT NOT NULL');
        $this->addSql('ALTER TABLE insuline ADD CONSTRAINT FK_3DDB287B6B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_3DDB287B6B899279 ON insuline (patient_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE insuline DROP CONSTRAINT FK_3DDB287B6B899279');
        $this->addSql('DROP INDEX IDX_3DDB287B6B899279');
        $this->addSql('ALTER TABLE insuline DROP patient_id');
    }
}
