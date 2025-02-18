<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218092229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE performance (id_performance INT AUTO_INCREMENT NOT NULL, id_swimmer INT NOT NULL, id_competition INT NOT NULL, historic VARCHAR(255) NOT NULL, position INT NOT NULL, time INT NOT NULL, INDEX IDX_82D7968133B66CA2 (id_swimmer), INDEX IDX_82D79681AD18E146 (id_competition), PRIMARY KEY(id_performance)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE performance ADD CONSTRAINT FK_82D7968133B66CA2 FOREIGN KEY (id_swimmer) REFERENCES swimmer (id_swimmer) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE performance ADD CONSTRAINT FK_82D79681AD18E146 FOREIGN KEY (id_competition) REFERENCES competition (id_competition) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE performance DROP FOREIGN KEY FK_82D7968133B66CA2');
        $this->addSql('ALTER TABLE performance DROP FOREIGN KEY FK_82D79681AD18E146');
        $this->addSql('DROP TABLE performance');
    }
}
