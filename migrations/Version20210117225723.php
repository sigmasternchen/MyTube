<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210117225723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD creator_id BINARY(16) DEFAULT NULL, ADD created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE id id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64961220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64961220EA6 ON user (creator_id)');
        $this->addSql('ALTER TABLE video CHANGE id id BINARY(16) NOT NULL, CHANGE uploader_id uploader_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE video_link CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE creator_id creator_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE view CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE link_id link_id BINARY(16) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64961220EA6');
        $this->addSql('DROP INDEX IDX_8D93D64961220EA6 ON user');
        $this->addSql('ALTER TABLE user DROP creator_id, DROP created, CHANGE id id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE video CHANGE id id BINARY(16) NOT NULL, CHANGE uploader_id uploader_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE video_link CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE creator_id creator_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE `view` CHANGE id id BINARY(16) NOT NULL, CHANGE video_id video_id BINARY(16) NOT NULL, CHANGE link_id link_id BINARY(16) NOT NULL');
    }
}
