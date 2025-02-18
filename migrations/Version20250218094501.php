<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218094501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76F373DCF');
        $this->addSql('DROP INDEX IDX_880E0D76F373DCF ON `admin`');
        $this->addSql('ALTER TABLE `admin` ADD nom_admin VARCHAR(255) NOT NULL, ADD prenom_admin VARCHAR(255) NOT NULL, ADD email_admin VARCHAR(255) NOT NULL, ADD password_admin VARCHAR(255) NOT NULL, DROP groups_id, DROP title_admin, DROP start_date, DROP duration, DROP intensity, DROP category, DROP description, DROP is_defined');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D7615434CF9 ON `admin` (email_admin)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_880E0D7615434CF9 ON `admin`');
        $this->addSql('ALTER TABLE `admin` ADD groups_id INT DEFAULT NULL, ADD title_admin VARCHAR(255) NOT NULL, ADD start_date DATETIME NOT NULL, ADD duration INT NOT NULL, ADD intensity VARCHAR(255) NOT NULL, ADD category VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL, ADD is_defined TINYINT(1) NOT NULL, DROP nom_admin, DROP prenom_admin, DROP email_admin, DROP password_admin');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D76F373DCF FOREIGN KEY (groups_id) REFERENCES `groups` (groups_id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_880E0D76F373DCF ON `admin` (groups_id)');
    }
}
