<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210108165208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE id id BINARY(16) NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE video CHANGE id id BINARY(16) NOT NULL, CHANGE uploader_id uploader_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE video_link CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE creator_id creator_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE view DROP FOREIGN KEY FK_FEFDAB8E29C1004E');
        $this->addSql('ALTER TABLE view DROP FOREIGN KEY FK_FEFDAB8EADA40271');
        $this->addSql('ALTER TABLE view CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE link_id link_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE view ADD CONSTRAINT FK_FEFDAB8E29C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE view ADD CONSTRAINT FK_FEFDAB8EADA40271 FOREIGN KEY (link_id) REFERENCES video_link (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE id id BINARY(16) NOT NULL, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE video CHANGE id id BINARY(16) NOT NULL, CHANGE uploader_id uploader_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE video_link CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE creator_id creator_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE `view` DROP FOREIGN KEY FK_FEFDAB8E29C1004E');
        $this->addSql('ALTER TABLE `view` DROP FOREIGN KEY FK_FEFDAB8EADA40271');
        $this->addSql('ALTER TABLE `view` CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE link_id link_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE `view` ADD CONSTRAINT FK_FEFDAB8E29C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE `view` ADD CONSTRAINT FK_FEFDAB8EADA40271 FOREIGN KEY (link_id) REFERENCES video_link (id)');
    }
}
