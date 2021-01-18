<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210118175842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL, creator_id BINARY(16) DEFAULT NULL, email VARCHAR(180) NOT NULL, name VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6495E237E06 (name), INDEX IDX_8D93D64961220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id BINARY(16) NOT NULL, uploader_id BINARY(16) NOT NULL, uploaded DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, description VARCHAR(1024) NOT NULL, tags LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', state INT NOT NULL, length DOUBLE PRECISION DEFAULT NULL, transcoding_progress INT NOT NULL, INDEX IDX_7CC7DA2C16678C77 (uploader_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video_link (id BINARY(16) NOT NULL, video_id BINARY(16) NOT NULL, creator_id BINARY(16) NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', max_views INT DEFAULT NULL, viewable_for INT DEFAULT NULL, viewable_until DATETIME DEFAULT NULL, comment VARCHAR(1024) DEFAULT NULL, INDEX IDX_313BC42D29C1004E (video_id), INDEX IDX_313BC42D61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `view` (id BINARY(16) NOT NULL, video_id BINARY(16) NOT NULL, link_id BINARY(16) DEFAULT NULL, timestamp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', validated DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FEFDAB8E29C1004E (video_id), INDEX IDX_FEFDAB8EADA40271 (link_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64961220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C16678C77 FOREIGN KEY (uploader_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE video_link ADD CONSTRAINT FK_313BC42D29C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video_link ADD CONSTRAINT FK_313BC42D61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `view` ADD CONSTRAINT FK_FEFDAB8E29C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `view` ADD CONSTRAINT FK_FEFDAB8EADA40271 FOREIGN KEY (link_id) REFERENCES video_link (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64961220EA6');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C16678C77');
        $this->addSql('ALTER TABLE video_link DROP FOREIGN KEY FK_313BC42D61220EA6');
        $this->addSql('ALTER TABLE video_link DROP FOREIGN KEY FK_313BC42D29C1004E');
        $this->addSql('ALTER TABLE `view` DROP FOREIGN KEY FK_FEFDAB8E29C1004E');
        $this->addSql('ALTER TABLE `view` DROP FOREIGN KEY FK_FEFDAB8EADA40271');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE video');
        $this->addSql('DROP TABLE video_link');
        $this->addSql('DROP TABLE `view`');
    }
}
