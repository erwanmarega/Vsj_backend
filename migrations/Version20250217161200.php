<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217161200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id_admin INT AUTO_INCREMENT NOT NULL, groups_id INT DEFAULT NULL, title_admin VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, duration INT NOT NULL, intensity VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, is_defined TINYINT(1) NOT NULL, roles JSON NOT NULL, INDEX IDX_880E0D76F373DCF (groups_id), PRIMARY KEY(id_admin)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coach (id_coach INT AUTO_INCREMENT NOT NULL, groups_id INT DEFAULT NULL, nom_coach VARCHAR(255) NOT NULL, prenom_coach VARCHAR(255) NOT NULL, tel_coach VARCHAR(20) DEFAULT NULL, email_coach VARCHAR(255) NOT NULL, password_coach VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_3F596DCCA2142C43 (email_coach), INDEX IDX_3F596DCCF373DCF (groups_id), PRIMARY KEY(id_coach)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition (id_competition INT AUTO_INCREMENT NOT NULL, groups_id INT DEFAULT NULL, title_competition VARCHAR(255) NOT NULL, day_competition DATE NOT NULL, hour_competition TIME NOT NULL, duration_competition INT NOT NULL, address_competition VARCHAR(255) NOT NULL, category_competition VARCHAR(255) NOT NULL, description_competition LONGTEXT DEFAULT NULL, is_defined_competition TINYINT(1) NOT NULL, INDEX IDX_B50A2CB1F373DCF (groups_id), PRIMARY KEY(id_competition)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `groups` (groups_id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(groups_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id_message INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, receiver_id INT DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, subject VARCHAR(255) NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FCD53EDB6 (receiver_id), PRIMARY KEY(id_message)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE swimmer (id_swimmer INT AUTO_INCREMENT NOT NULL, groups_id INT DEFAULT NULL, nom_swimmer VARCHAR(255) DEFAULT NULL, prenom_swimmer VARCHAR(255) DEFAULT NULL, date_naissance_swimmer DATE DEFAULT NULL, email_swimmer VARCHAR(255) NOT NULL, roles JSON NOT NULL, password_swimmer VARCHAR(255) NOT NULL, adresse_swimmer VARCHAR(255) DEFAULT NULL, code_postal_swimmer VARCHAR(10) DEFAULT NULL, ville_swimmer VARCHAR(255) DEFAULT NULL, telephone_swimmer VARCHAR(20) DEFAULT NULL, level INT DEFAULT NULL, crawl INT DEFAULT NULL, papillon INT DEFAULT NULL, dos_crawl INT DEFAULT NULL, brasse INT DEFAULT NULL, UNIQUE INDEX UNIQ_ED2BC5D250EBAD1D (email_swimmer), INDEX IDX_ED2BC5D2F373DCF (groups_id), PRIMARY KEY(id_swimmer)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training (id_training INT AUTO_INCREMENT NOT NULL, groups_id INT DEFAULT NULL, title_training VARCHAR(255) NOT NULL, date_training DATETIME NOT NULL, duration_training INT NOT NULL, intensity_training VARCHAR(255) NOT NULL, category_training VARCHAR(255) NOT NULL, description_training LONGTEXT DEFAULT NULL, is_defined_training TINYINT(1) NOT NULL, INDEX IDX_D5128A8FF373DCF (groups_id), PRIMARY KEY(id_training)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D76F373DCF FOREIGN KEY (groups_id) REFERENCES `groups` (groups_id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCCF373DCF FOREIGN KEY (groups_id) REFERENCES `groups` (groups_id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB1F373DCF FOREIGN KEY (groups_id) REFERENCES `groups` (groups_id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES swimmer (id_swimmer)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES swimmer (id_swimmer)');
        $this->addSql('ALTER TABLE swimmer ADD CONSTRAINT FK_ED2BC5D2F373DCF FOREIGN KEY (groups_id) REFERENCES `groups` (groups_id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8FF373DCF FOREIGN KEY (groups_id) REFERENCES `groups` (groups_id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76F373DCF');
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCCF373DCF');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB1F373DCF');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE swimmer DROP FOREIGN KEY FK_ED2BC5D2F373DCF');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8FF373DCF');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE coach');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE `groups`');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE swimmer');
        $this->addSql('DROP TABLE training');
    }
}
