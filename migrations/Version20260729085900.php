<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260729085900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal ADD dish_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE meal DROP type');
        $this->addSql('ALTER TABLE meal DROP created_at');
        $this->addSql('ALTER TABLE meal_item ALTER meal_id SET NOT NULL');
        $this->addSql('ALTER TABLE meal_item ALTER aliment_reference_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal ADD type VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE meal ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE meal DROP dish_name');
        $this->addSql('ALTER TABLE meal_item ALTER meal_id DROP NOT NULL');
        $this->addSql('ALTER TABLE meal_item ALTER aliment_reference_id DROP NOT NULL');
    }
}
