<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260925083643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la relation entre MealItem et Meal et rattache les anciennes lignes au repas existant.';
    }

    public function up(Schema $schema): void
    {
        // 1. Création temporaire de meal_id en nullable
        $this->addSql('ALTER TABLE meal_item ADD meal_id INT DEFAULT NULL');

        // 2. Les anciennes lignes correspondent au repas existant #1
        $this->addSql('UPDATE meal_item SET meal_id = 1 WHERE meal_id IS NULL');

        // 3. Une ligne de repas doit obligatoirement appartenir à un repas
        $this->addSql('ALTER TABLE meal_item ALTER COLUMN meal_id SET NOT NULL');

        // 4. Clé étrangère vers meal
        $this->addSql(
            'ALTER TABLE meal_item
             ADD CONSTRAINT FK_5C5F6EA639666D6
             FOREIGN KEY (meal_id)
             REFERENCES meal (id)
             ON DELETE CASCADE
             NOT DEFERRABLE'
        );

        // 5. Index
        $this->addSql(
            'CREATE INDEX IDX_5C5F6EA639666D6 ON meal_item (meal_id)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE meal_item DROP CONSTRAINT FK_5C5F6EA639666D6'
        );

        $this->addSql(
            'DROP INDEX IDX_5C5F6EA639666D6'
        );

        $this->addSql(
            'ALTER TABLE meal_item DROP meal_id'
        );
    }
}