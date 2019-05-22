<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190521083802 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movie_night ADD movie_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE movie_night ADD CONSTRAINT FK_6F98150B10684CB FOREIGN KEY (movie_id_id) REFERENCES movie (id)');
        $this->addSql('CREATE INDEX IDX_6F98150B10684CB ON movie_night (movie_id_id)');
        $this->addSql('ALTER TABLE movie CHANGE poster poster VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movie CHANGE poster poster VARCHAR(512) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE movie_night DROP FOREIGN KEY FK_6F98150B10684CB');
        $this->addSql('DROP INDEX IDX_6F98150B10684CB ON movie_night');
        $this->addSql('ALTER TABLE movie_night DROP movie_id_id');
    }
}
