<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210118172542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE id id BINARY(16) NOT NULL, CHANGE creator_id creator_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE video CHANGE id id BINARY(16) NOT NULL, CHANGE uploader_id uploader_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE video_link CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE creator_id creator_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE view DROP FOREIGN KEY FK_FEFDAB8EADA40271');
        $this->addSql('ALTER TABLE view CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE link_id link_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE view ADD CONSTRAINT FK_FEFDAB8EADA40271 FOREIGN KEY (link_id) REFERENCES video_link (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE id id BINARY(16) NOT NULL, CHANGE creator_id creator_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE video CHANGE id id BINARY(16) NOT NULL, CHANGE uploader_id uploader_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE video_link CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE creator_id creator_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE `view` DROP FOREIGN KEY FK_FEFDAB8EADA40271');
        $this->addSql('ALTER TABLE `view` CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE link_id link_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE `view` ADD CONSTRAINT FK_FEFDAB8EADA40271 FOREIGN KEY (link_id) REFERENCES video_link (id) ON DELETE CASCADE');
    }
}
