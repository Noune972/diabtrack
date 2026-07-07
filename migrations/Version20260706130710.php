<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260706130710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blood_sugar ADD relation VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE blood_sugar ADD patient_id INT NOT NULL');
        $this->addSql('ALTER TABLE blood_sugar ADD CONSTRAINT FK_CD3209BF6B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_CD3209BF6B899279 ON blood_sugar (patient_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blood_sugar DROP CONSTRAINT FK_CD3209BF6B899279');
        $this->addSql('DROP INDEX IDX_CD3209BF6B899279');
        $this->addSql('ALTER TABLE blood_sugar DROP relation');
        $this->addSql('ALTER TABLE blood_sugar DROP patient_id');
    }
}
