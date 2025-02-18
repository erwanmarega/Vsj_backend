<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218091327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attendance (id_attendance INT AUTO_INCREMENT NOT NULL, id_swimmer INT NOT NULL, id_training INT NOT NULL, historic VARCHAR(255) NOT NULL, is_attendance TINYINT(1) NOT NULL, INDEX IDX_6DE30D9133B66CA2 (id_swimmer), INDEX IDX_6DE30D9185C9661A (id_training), PRIMARY KEY(id_attendance)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D9133B66CA2 FOREIGN KEY (id_swimmer) REFERENCES swimmer (id_swimmer) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D9185C9661A FOREIGN KEY (id_training) REFERENCES training (id_training) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D9133B66CA2');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D9185C9661A');
        $this->addSql('DROP TABLE attendance');
    }
}
