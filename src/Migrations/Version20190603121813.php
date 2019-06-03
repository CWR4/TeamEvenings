<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190603121813 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movie_night ADD voting_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE movie_night ADD CONSTRAINT FK_6F98150B4254ACF8 FOREIGN KEY (voting_id) REFERENCES voting (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6F98150B4254ACF8 ON movie_night (voting_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movie_night DROP FOREIGN KEY FK_6F98150B4254ACF8');
        $this->addSql('DROP INDEX UNIQ_6F98150B4254ACF8 ON movie_night');
        $this->addSql('ALTER TABLE movie_night DROP voting_id');
    }
}
