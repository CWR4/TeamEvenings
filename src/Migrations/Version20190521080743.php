<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190521080743 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE movie_night (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, time TIME NOT NULL, location VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE movies');
        $this->addSql('ALTER TABLE movie CHANGE poster poster VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE movies (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, imdb_id VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, year VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, runtime VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, poster VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, plot VARCHAR(1000) DEFAULT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE movie_night');
        $this->addSql('ALTER TABLE movie CHANGE poster poster VARCHAR(512) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
