<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260707080137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hba1c ADD patient_id INT NOT NULL');
        $this->addSql('ALTER TABLE hba1c ADD CONSTRAINT FK_A5BC625E6B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_A5BC625E6B899279 ON hba1c (patient_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hba1c DROP CONSTRAINT FK_A5BC625E6B899279');
        $this->addSql('DROP INDEX IDX_A5BC625E6B899279');
        $this->addSql('ALTER TABLE hba1c DROP patient_id');
    }
}
