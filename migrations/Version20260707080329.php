<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260707080329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reminder ADD patient_id INT NOT NULL');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F406B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_40374F406B899279 ON reminder (patient_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reminder DROP CONSTRAINT FK_40374F406B899279');
        $this->addSql('DROP INDEX IDX_40374F406B899279');
        $this->addSql('ALTER TABLE reminder DROP patient_id');
    }
}
