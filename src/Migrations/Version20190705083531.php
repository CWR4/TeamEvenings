<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190705083531 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_night (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, time TIME NOT NULL, location VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, imdb_id VARCHAR(255) NOT NULL, year VARCHAR(255) NOT NULL, runtime VARCHAR(255) DEFAULT NULL, poster VARCHAR(255) DEFAULT NULL, plot VARCHAR(1000) DEFAULT NULL, UNIQUE INDEX UNIQ_1D5EF26F53B538EB (imdb_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_movie_night (movie_id INT NOT NULL, movie_night_id INT NOT NULL, INDEX IDX_401E99FC8F93B6FC (movie_id), INDEX IDX_401E99FCA375D7DC (movie_night_id), PRIMARY KEY(movie_id, movie_night_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, movie_id INT NOT NULL, user_id INT NOT NULL, movie_night_id INT NOT NULL, INDEX IDX_5A1085648F93B6FC (movie_id), INDEX IDX_5A108564A76ED395 (user_id), INDEX IDX_5A108564A375D7DC (movie_night_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movie_movie_night ADD CONSTRAINT FK_401E99FC8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_movie_night ADD CONSTRAINT FK_401E99FCA375D7DC FOREIGN KEY (movie_night_id) REFERENCES movie_night (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A1085648F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A375D7DC FOREIGN KEY (movie_night_id) REFERENCES movie_night (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564A76ED395');
        $this->addSql('ALTER TABLE movie_movie_night DROP FOREIGN KEY FK_401E99FCA375D7DC');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564A375D7DC');
        $this->addSql('ALTER TABLE movie_movie_night DROP FOREIGN KEY FK_401E99FC8F93B6FC');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A1085648F93B6FC');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE movie_night');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE movie_movie_night');
        $this->addSql('DROP TABLE vote');
    }
}