<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210107200117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "view" (id BLOB NOT NULL, video_id BLOB NOT NULL, link_id BLOB NOT NULL, timestamp DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , validated DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FEFDAB8E29C1004E ON "view" (video_id)');
        $this->addSql('CREATE INDEX IDX_FEFDAB8EADA40271 ON "view" (link_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D6495E237E06');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, password, name, roles FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id BLOB NOT NULL, password VARCHAR(255) NOT NULL COLLATE BINARY, name VARCHAR(180) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, password, name, roles) SELECT id, password, name, roles FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495E237E06 ON user (name)');
        $this->addSql('DROP INDEX IDX_7CC7DA2C16678C77');
        $this->addSql('CREATE TEMPORARY TABLE __temp__video AS SELECT id, uploader_id, uploaded, name, description, tags, state, length, transcoding_progress FROM video');
        $this->addSql('DROP TABLE video');
        $this->addSql('CREATE TABLE video (id BLOB NOT NULL, uploader_id BLOB NOT NULL, uploaded DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , name VARCHAR(255) NOT NULL COLLATE BINARY, description VARCHAR(1024) NOT NULL COLLATE BINARY, tags CLOB NOT NULL COLLATE BINARY --(DC2Type:array)
        , state INTEGER NOT NULL, length DOUBLE PRECISION DEFAULT NULL, transcoding_progress INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_7CC7DA2C16678C77 FOREIGN KEY (uploader_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO video (id, uploader_id, uploaded, name, description, tags, state, length, transcoding_progress) SELECT id, uploader_id, uploaded, name, description, tags, state, length, transcoding_progress FROM __temp__video');
        $this->addSql('DROP TABLE __temp__video');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C16678C77 ON video (uploader_id)');
        $this->addSql('DROP INDEX IDX_313BC42D61220EA6');
        $this->addSql('DROP INDEX IDX_313BC42D29C1004E');
        $this->addSql('CREATE TEMPORARY TABLE __temp__video_link AS SELECT id, video_id, creator_id, created, max_views, viewable_for, viewable_until, comment FROM video_link');
        $this->addSql('DROP TABLE video_link');
        $this->addSql('CREATE TABLE video_link (id BLOB NOT NULL, video_id BLOB NOT NULL, creator_id BLOB NOT NULL, created DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , max_views INTEGER DEFAULT NULL, viewable_for INTEGER DEFAULT NULL, viewable_until DATETIME DEFAULT NULL, comment VARCHAR(1024) DEFAULT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_313BC42D29C1004E FOREIGN KEY (video_id) REFERENCES video (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_313BC42D61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO video_link (id, video_id, creator_id, created, max_views, viewable_for, viewable_until, comment) SELECT id, video_id, creator_id, created, max_views, viewable_for, viewable_until, comment FROM __temp__video_link');
        $this->addSql('DROP TABLE __temp__video_link');
        $this->addSql('CREATE INDEX IDX_313BC42D61220EA6 ON video_link (creator_id)');
        $this->addSql('CREATE INDEX IDX_313BC42D29C1004E ON video_link (video_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "view"');
        $this->addSql('DROP INDEX UNIQ_8D93D6495E237E06');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, name, roles, password FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id BLOB NOT NULL, name VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, name, roles, password) SELECT id, name, roles, password FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495E237E06 ON user (name)');
        $this->addSql('DROP INDEX IDX_7CC7DA2C16678C77');
        $this->addSql('CREATE TEMPORARY TABLE __temp__video AS SELECT id, uploader_id, uploaded, name, description, tags, state, length, transcoding_progress FROM video');
        $this->addSql('DROP TABLE video');
        $this->addSql('CREATE TABLE video (id BLOB NOT NULL, uploaded DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , name VARCHAR(255) NOT NULL, description VARCHAR(1024) NOT NULL, tags CLOB NOT NULL --(DC2Type:array)
        , state INTEGER NOT NULL, length DOUBLE PRECISION DEFAULT NULL, uploader_id BLOB NOT NULL, transcoding_progress INTEGER DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO video (id, uploader_id, uploaded, name, description, tags, state, length, transcoding_progress) SELECT id, uploader_id, uploaded, name, description, tags, state, length, transcoding_progress FROM __temp__video');
        $this->addSql('DROP TABLE __temp__video');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C16678C77 ON video (uploader_id)');
        $this->addSql('DROP INDEX IDX_313BC42D29C1004E');
        $this->addSql('DROP INDEX IDX_313BC42D61220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__video_link AS SELECT id, video_id, creator_id, created, max_views, viewable_for, viewable_until, comment FROM video_link');
        $this->addSql('DROP TABLE video_link');
        $this->addSql('CREATE TABLE video_link (id BLOB NOT NULL, created DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , max_views INTEGER DEFAULT NULL, viewable_for INTEGER DEFAULT NULL, viewable_until DATETIME DEFAULT NULL, comment VARCHAR(1024) DEFAULT NULL, video_id BLOB NOT NULL, creator_id BLOB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO video_link (id, video_id, creator_id, created, max_views, viewable_for, viewable_until, comment) SELECT id, video_id, creator_id, created, max_views, viewable_for, viewable_until, comment FROM __temp__video_link');
        $this->addSql('DROP TABLE __temp__video_link');
        $this->addSql('CREATE INDEX IDX_313BC42D29C1004E ON video_link (video_id)');
        $this->addSql('CREATE INDEX IDX_313BC42D61220EA6 ON video_link (creator_id)');
    }
}
