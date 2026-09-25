<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260925070746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD adress VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD postal_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD height INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP phone');
        $this->addSql('ALTER TABLE "user" DROP adress');
        $this->addSql('ALTER TABLE "user" DROP postal_code');
        $this->addSql('ALTER TABLE "user" DROP city');
        $this->addSql('ALTER TABLE "user" DROP height');
    }
}
