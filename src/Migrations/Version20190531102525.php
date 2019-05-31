<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190531102525 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, voting_id INT NOT NULL, movie_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5A1085644254ACF8 (voting_id), INDEX IDX_5A1085648F93B6FC (movie_id), INDEX IDX_5A108564A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voting (id INT AUTO_INCREMENT NOT NULL, open TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voting_movie (voting_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_D138ED2A4254ACF8 (voting_id), INDEX IDX_D138ED2A8F93B6FC (movie_id), PRIMARY KEY(voting_id, movie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A1085644254ACF8 FOREIGN KEY (voting_id) REFERENCES voting (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A1085648F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE voting_movie ADD CONSTRAINT FK_D138ED2A4254ACF8 FOREIGN KEY (voting_id) REFERENCES voting (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE voting_movie ADD CONSTRAINT FK_D138ED2A8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A1085644254ACF8');
        $this->addSql('ALTER TABLE voting_movie DROP FOREIGN KEY FK_D138ED2A4254ACF8');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE voting');
        $this->addSql('DROP TABLE voting_movie');
    }
}
