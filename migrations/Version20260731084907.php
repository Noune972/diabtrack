<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260731084907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blood_sugar DROP CONSTRAINT fk_cd3209bf6b899279');
        $this->addSql('ALTER TABLE blood_sugar ADD CONSTRAINT FK_CD3209BF6B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526c6b899279');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526c1f55203d');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C6B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE comment_article DROP CONSTRAINT fk_f1496c766b899279');
        $this->addSql('ALTER TABLE comment_article DROP CONSTRAINT fk_f1496c767294869c');
        $this->addSql('ALTER TABLE comment_article ADD CONSTRAINT FK_F1496C766B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE comment_article ADD CONSTRAINT FK_F1496C767294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE hba1c DROP CONSTRAINT fk_a5bc625e6b899279');
        $this->addSql('ALTER TABLE hba1c ADD CONSTRAINT FK_A5BC625E6B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE insuline DROP CONSTRAINT fk_3ddb287b6b899279');
        $this->addSql('ALTER TABLE insuline ADD CONSTRAINT FK_3DDB287B6B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE meal DROP CONSTRAINT fk_9ef68e9c6b899279');
        $this->addSql('ALTER TABLE meal ADD CONSTRAINT FK_9EF68E9C6B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE meal_item DROP CONSTRAINT fk_5c5f6ea639666d6');
        $this->addSql('DROP INDEX idx_5c5f6ea639666d6');
        $this->addSql('ALTER TABLE meal_item DROP meal_id');
        $this->addSql('ALTER TABLE reminder DROP CONSTRAINT fk_40374f406b899279');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F406B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT fk_7ce748aa76ed395');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE sporting_activity DROP CONSTRAINT fk_a6829ced6b899279');
        $this->addSql('ALTER TABLE sporting_activity ADD CONSTRAINT FK_A6829CED6B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE topic DROP CONSTRAINT fk_9d40de1b6b899279');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B6B899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blood_sugar DROP CONSTRAINT FK_CD3209BF6B899279');
        $this->addSql('ALTER TABLE blood_sugar ADD CONSTRAINT fk_cd3209bf6b899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C6B899279');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C1F55203D');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526c6b899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526c1f55203d FOREIGN KEY (topic_id) REFERENCES topic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment_article DROP CONSTRAINT FK_F1496C766B899279');
        $this->addSql('ALTER TABLE comment_article DROP CONSTRAINT FK_F1496C767294869C');
        $this->addSql('ALTER TABLE comment_article ADD CONSTRAINT fk_f1496c766b899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment_article ADD CONSTRAINT fk_f1496c767294869c FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hba1c DROP CONSTRAINT FK_A5BC625E6B899279');
        $this->addSql('ALTER TABLE hba1c ADD CONSTRAINT fk_a5bc625e6b899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE insuline DROP CONSTRAINT FK_3DDB287B6B899279');
        $this->addSql('ALTER TABLE insuline ADD CONSTRAINT fk_3ddb287b6b899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE meal DROP CONSTRAINT FK_9EF68E9C6B899279');
        $this->addSql('ALTER TABLE meal ADD CONSTRAINT fk_9ef68e9c6b899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE meal_item ADD meal_id INT NOT NULL');
        $this->addSql('ALTER TABLE meal_item ADD CONSTRAINT fk_5c5f6ea639666d6 FOREIGN KEY (meal_id) REFERENCES meal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5c5f6ea639666d6 ON meal_item (meal_id)');
        $this->addSql('ALTER TABLE reminder DROP CONSTRAINT FK_40374F406B899279');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT fk_40374f406b899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT fk_7ce748aa76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sporting_activity DROP CONSTRAINT FK_A6829CED6B899279');
        $this->addSql('ALTER TABLE sporting_activity ADD CONSTRAINT fk_a6829ced6b899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE topic DROP CONSTRAINT FK_9D40DE1B6B899279');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT fk_9d40de1b6b899279 FOREIGN KEY (patient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
