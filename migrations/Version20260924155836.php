<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260924155836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE glycemic_target ALTER fasting_min TYPE NUMERIC(4, 2)');
        $this->addSql('ALTER TABLE glycemic_target ALTER fasting_max TYPE NUMERIC(4, 2)');
        $this->addSql('ALTER TABLE glycemic_target ALTER post_meal_min TYPE NUMERIC(4, 2)');
        $this->addSql('ALTER TABLE glycemic_target ALTER post_meal_max TYPE NUMERIC(4, 2)');
        $this->addSql('ALTER TABLE glycemic_target ALTER bedtime_min TYPE NUMERIC(4, 2)');
        $this->addSql('ALTER TABLE glycemic_target ALTER bedtime_max TYPE NUMERIC(4, 2)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE glycemic_target ALTER fasting_min TYPE INT');
        $this->addSql('ALTER TABLE glycemic_target ALTER fasting_max TYPE INT');
        $this->addSql('ALTER TABLE glycemic_target ALTER post_meal_min TYPE INT');
        $this->addSql('ALTER TABLE glycemic_target ALTER post_meal_max TYPE INT');
        $this->addSql('ALTER TABLE glycemic_target ALTER bedtime_min TYPE INT');
        $this->addSql('ALTER TABLE glycemic_target ALTER bedtime_max TYPE INT');
    }
}
