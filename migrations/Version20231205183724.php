<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231214144417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bibliogame (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, member_id INT NOT NULL, borrowed_by_id INT DEFAULT NULL, request_by_id INT DEFAULT NULL, is_available TINYINT(1) DEFAULT NULL, borrowed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2E765A0DE48FD905 (game_id), INDEX IDX_2E765A0D7597D3FE (member_id), INDEX IDX_2E765A0D39759382 (borrowed_by_id), INDEX IDX_2E765A0DF7F09C21 (request_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chatting (id INT AUTO_INCREMENT NOT NULL, user_from_id INT DEFAULT NULL, user_to_id INT DEFAULT NULL, INDEX IDX_C71695F520C3C701 (user_from_id), INDEX IDX_C71695F5D2F7B13D (user_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, alias VARCHAR(50) NOT NULL, email VARCHAR(40) NOT NULL, subject VARCHAR(40) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE editor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, slug VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, host_id INT NOT NULL, title VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, picture VARCHAR(255) DEFAULT NULL, players_min INT DEFAULT NULL, players_max INT DEFAULT NULL, address VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, is_public TINYINT(1) NOT NULL, start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3BAE0AA71FB8D185 (host_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_game (event_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_3CE07D2771F7E88B (event_id), INDEX IDX_3CE07D27E48FD905 (game_id), PRIMARY KEY(event_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_requests (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, event_id INT NOT NULL, status VARCHAR(10) NOT NULL, INDEX IDX_3D693F81A76ED395 (user_id), INDEX IDX_3D693F8171F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, editor_id INT DEFAULT NULL, title VARCHAR(200) NOT NULL, minimum_age INT DEFAULT NULL, released_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', rating DOUBLE PRECISION DEFAULT NULL, duration INT DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, players_min INT NOT NULL, players_max INT DEFAULT NULL, slug VARCHAR(200) NOT NULL, short_description LONGTEXT NOT NULL, long_description LONGTEXT DEFAULT NULL, INDEX IDX_232B318C6995AC4C (editor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_author (game_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_C09A7500E48FD905 (game_id), INDEX IDX_C09A7500F675F31B (author_id), PRIMARY KEY(game_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_illustrator (game_id INT NOT NULL, illustrator_id INT NOT NULL, INDEX IDX_495A53B5E48FD905 (game_id), INDEX IDX_495A53B5653613B3 (illustrator_id), PRIMARY KEY(game_id, illustrator_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_type (game_id INT NOT NULL, type_id INT NOT NULL, INDEX IDX_67CB3B05E48FD905 (game_id), INDEX IDX_67CB3B05C54C8C93 (type_id), PRIMARY KEY(game_id, type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_theme (game_id INT NOT NULL, theme_id INT NOT NULL, INDEX IDX_A5469E87E48FD905 (game_id), INDEX IDX_A5469E8759027487 (theme_id), PRIMARY KEY(game_id, theme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE illustrator (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, chatting_id INT NOT NULL, content VARCHAR(255) NOT NULL, sent_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B6BD307F2DACBFC2 (chatting_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, member_id INT NOT NULL, content LONGTEXT NOT NULL, rating DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_794381C6E48FD905 (game_id), INDEX IDX_794381C67597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, slug VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, slug VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, firstname VARCHAR(25) NOT NULL, lastname VARCHAR(25) NOT NULL, birth_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', address VARCHAR(100) NOT NULL, city VARCHAR(50) NOT NULL, longitude DOUBLE PRECISION DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(6) NOT NULL, alias VARCHAR(50) NOT NULL, reset_token VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649E16C6B94 (alias), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bibliogame ADD CONSTRAINT FK_2E765A0DE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE bibliogame ADD CONSTRAINT FK_2E765A0D7597D3FE FOREIGN KEY (member_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bibliogame ADD CONSTRAINT FK_2E765A0D39759382 FOREIGN KEY (borrowed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bibliogame ADD CONSTRAINT FK_2E765A0DF7F09C21 FOREIGN KEY (request_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE chatting ADD CONSTRAINT FK_C71695F520C3C701 FOREIGN KEY (user_from_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE chatting ADD CONSTRAINT FK_C71695F5D2F7B13D FOREIGN KEY (user_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA71FB8D185 FOREIGN KEY (host_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_game ADD CONSTRAINT FK_3CE07D2771F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_game ADD CONSTRAINT FK_3CE07D27E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_requests ADD CONSTRAINT FK_3D693F81A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_requests ADD CONSTRAINT FK_3D693F8171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C6995AC4C FOREIGN KEY (editor_id) REFERENCES editor (id)');
        $this->addSql('ALTER TABLE game_author ADD CONSTRAINT FK_C09A7500E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_author ADD CONSTRAINT FK_C09A7500F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_illustrator ADD CONSTRAINT FK_495A53B5E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_illustrator ADD CONSTRAINT FK_495A53B5653613B3 FOREIGN KEY (illustrator_id) REFERENCES illustrator (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_type ADD CONSTRAINT FK_67CB3B05E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_type ADD CONSTRAINT FK_67CB3B05C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_theme ADD CONSTRAINT FK_A5469E87E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_theme ADD CONSTRAINT FK_A5469E8759027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2DACBFC2 FOREIGN KEY (chatting_id) REFERENCES chatting (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C67597D3FE FOREIGN KEY (member_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bibliogame DROP FOREIGN KEY FK_2E765A0DE48FD905');
        $this->addSql('ALTER TABLE bibliogame DROP FOREIGN KEY FK_2E765A0D7597D3FE');
        $this->addSql('ALTER TABLE bibliogame DROP FOREIGN KEY FK_2E765A0D39759382');
        $this->addSql('ALTER TABLE bibliogame DROP FOREIGN KEY FK_2E765A0DF7F09C21');
        $this->addSql('ALTER TABLE chatting DROP FOREIGN KEY FK_C71695F520C3C701');
        $this->addSql('ALTER TABLE chatting DROP FOREIGN KEY FK_C71695F5D2F7B13D');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA71FB8D185');
        $this->addSql('ALTER TABLE event_game DROP FOREIGN KEY FK_3CE07D2771F7E88B');
        $this->addSql('ALTER TABLE event_game DROP FOREIGN KEY FK_3CE07D27E48FD905');
        $this->addSql('ALTER TABLE event_requests DROP FOREIGN KEY FK_3D693F81A76ED395');
        $this->addSql('ALTER TABLE event_requests DROP FOREIGN KEY FK_3D693F8171F7E88B');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C6995AC4C');
        $this->addSql('ALTER TABLE game_author DROP FOREIGN KEY FK_C09A7500E48FD905');
        $this->addSql('ALTER TABLE game_author DROP FOREIGN KEY FK_C09A7500F675F31B');
        $this->addSql('ALTER TABLE game_illustrator DROP FOREIGN KEY FK_495A53B5E48FD905');
        $this->addSql('ALTER TABLE game_illustrator DROP FOREIGN KEY FK_495A53B5653613B3');
        $this->addSql('ALTER TABLE game_type DROP FOREIGN KEY FK_67CB3B05E48FD905');
        $this->addSql('ALTER TABLE game_type DROP FOREIGN KEY FK_67CB3B05C54C8C93');
        $this->addSql('ALTER TABLE game_theme DROP FOREIGN KEY FK_A5469E87E48FD905');
        $this->addSql('ALTER TABLE game_theme DROP FOREIGN KEY FK_A5469E8759027487');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F2DACBFC2');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6E48FD905');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C67597D3FE');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE bibliogame');
        $this->addSql('DROP TABLE chatting');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE editor');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_game');
        $this->addSql('DROP TABLE event_requests');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_author');
        $this->addSql('DROP TABLE game_illustrator');
        $this->addSql('DROP TABLE game_type');
        $this->addSql('DROP TABLE game_theme');
        $this->addSql('DROP TABLE illustrator');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE user');
    }
}