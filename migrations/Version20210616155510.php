<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210616155510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(500) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, birth_date DATE DEFAULT NULL, id_mongo VARCHAR(500) DEFAULT NULL, subscribe_date DATETIME NOT NULL, skills JSON DEFAULT NULL, role JSON DEFAULT NULL, is_verified TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE apply (id INT AUTO_INCREMENT NOT NULL, id_account_id INT NOT NULL, id_project_id INT NOT NULL, role_project_id INT NOT NULL, description VARCHAR(500) DEFAULT NULL, INDEX IDX_BD2F8C1F3EE1DF6D (id_account_id), INDEX IDX_BD2F8C1FB3E79F4B (id_project_id), INDEX IDX_BD2F8C1F995975B0 (role_project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ch_cookieconsent_log (id INT AUTO_INCREMENT NOT NULL, ip_address VARCHAR(255) NOT NULL, cookie_consent_key VARCHAR(255) NOT NULL, cookie_name VARCHAR(255) NOT NULL, cookie_value VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentary (id INT AUTO_INCREMENT NOT NULL, id_project_id INT NOT NULL, id_account_id INT DEFAULT NULL, comment VARCHAR(500) NOT NULL, date_comment DATETIME NOT NULL, INDEX IDX_1CAC12CAB3E79F4B (id_project_id), INDEX IDX_1CAC12CA3EE1DF6D (id_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE is_for (id INT AUTO_INCREMENT NOT NULL, id_account_id INT NOT NULL, id_project_id INT NOT NULL, evaluation TINYINT(1) NOT NULL, INDEX IDX_A311AA303EE1DF6D (id_account_id), INDEX IDX_A311AA30B3E79F4B (id_project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jobs_account (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, job_id INT NOT NULL, INDEX IDX_AFC63159B6B5FBA (account_id), INDEX IDX_AFC6315BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, repo VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(500) DEFAULT NULL, date_creation DATETIME NOT NULL, id_mongo VARCHAR(255) DEFAULT NULL, status INT NOT NULL, skills_needed JSON DEFAULT NULL, job_needed JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_comment (id INT AUTO_INCREMENT NOT NULL, commentary_id INT NOT NULL, account_id INT NOT NULL, reason VARCHAR(255) NOT NULL, description VARCHAR(500) NOT NULL, date_report DATETIME NOT NULL, is_treated TINYINT(1) NOT NULL, INDEX IDX_F4ED2F6C5DED49AA (commentary_id), INDEX IDX_F4ED2F6C9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_project (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, account_id INT NOT NULL, date_report DATETIME NOT NULL, reason VARCHAR(255) NOT NULL, description VARCHAR(500) NOT NULL, is_treated TINYINT(1) NOT NULL, INDEX IDX_4F2AADEE166D1F9C (project_id), INDEX IDX_4F2AADEE9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_user (id INT AUTO_INCREMENT NOT NULL, reporter_id INT NOT NULL, reported_id INT NOT NULL, date_report DATETIME NOT NULL, reason VARCHAR(255) NOT NULL, description VARCHAR(500) NOT NULL, is_treated TINYINT(1) NOT NULL, INDEX IDX_FEBF3BB2E1CFE6F5 (reporter_id), INDEX IDX_FEBF3BB294BDEEB6 (reported_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(500) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apply ADD CONSTRAINT FK_BD2F8C1F3EE1DF6D FOREIGN KEY (id_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE apply ADD CONSTRAINT FK_BD2F8C1FB3E79F4B FOREIGN KEY (id_project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE apply ADD CONSTRAINT FK_BD2F8C1F995975B0 FOREIGN KEY (role_project_id) REFERENCES role_project (id)');
        $this->addSql('ALTER TABLE commentary ADD CONSTRAINT FK_1CAC12CAB3E79F4B FOREIGN KEY (id_project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE commentary ADD CONSTRAINT FK_1CAC12CA3EE1DF6D FOREIGN KEY (id_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE is_for ADD CONSTRAINT FK_A311AA303EE1DF6D FOREIGN KEY (id_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE is_for ADD CONSTRAINT FK_A311AA30B3E79F4B FOREIGN KEY (id_project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE jobs_account ADD CONSTRAINT FK_AFC63159B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE jobs_account ADD CONSTRAINT FK_AFC6315BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE report_comment ADD CONSTRAINT FK_F4ED2F6C5DED49AA FOREIGN KEY (commentary_id) REFERENCES commentary (id)');
        $this->addSql('ALTER TABLE report_comment ADD CONSTRAINT FK_F4ED2F6C9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE report_project ADD CONSTRAINT FK_4F2AADEE166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE report_project ADD CONSTRAINT FK_4F2AADEE9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE report_user ADD CONSTRAINT FK_FEBF3BB2E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE report_user ADD CONSTRAINT FK_FEBF3BB294BDEEB6 FOREIGN KEY (reported_id) REFERENCES account (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apply DROP FOREIGN KEY FK_BD2F8C1F3EE1DF6D');
        $this->addSql('ALTER TABLE commentary DROP FOREIGN KEY FK_1CAC12CA3EE1DF6D');
        $this->addSql('ALTER TABLE is_for DROP FOREIGN KEY FK_A311AA303EE1DF6D');
        $this->addSql('ALTER TABLE jobs_account DROP FOREIGN KEY FK_AFC63159B6B5FBA');
        $this->addSql('ALTER TABLE report_comment DROP FOREIGN KEY FK_F4ED2F6C9B6B5FBA');
        $this->addSql('ALTER TABLE report_project DROP FOREIGN KEY FK_4F2AADEE9B6B5FBA');
        $this->addSql('ALTER TABLE report_user DROP FOREIGN KEY FK_FEBF3BB2E1CFE6F5');
        $this->addSql('ALTER TABLE report_user DROP FOREIGN KEY FK_FEBF3BB294BDEEB6');
        $this->addSql('ALTER TABLE report_comment DROP FOREIGN KEY FK_F4ED2F6C5DED49AA');
        $this->addSql('ALTER TABLE jobs_account DROP FOREIGN KEY FK_AFC6315BE04EA9');
        $this->addSql('ALTER TABLE apply DROP FOREIGN KEY FK_BD2F8C1FB3E79F4B');
        $this->addSql('ALTER TABLE commentary DROP FOREIGN KEY FK_1CAC12CAB3E79F4B');
        $this->addSql('ALTER TABLE is_for DROP FOREIGN KEY FK_A311AA30B3E79F4B');
        $this->addSql('ALTER TABLE report_project DROP FOREIGN KEY FK_4F2AADEE166D1F9C');
        $this->addSql('ALTER TABLE apply DROP FOREIGN KEY FK_BD2F8C1F995975B0');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE apply');
        $this->addSql('DROP TABLE ch_cookieconsent_log');
        $this->addSql('DROP TABLE commentary');
        $this->addSql('DROP TABLE is_for');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE jobs_account');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE report_comment');
        $this->addSql('DROP TABLE report_project');
        $this->addSql('DROP TABLE report_user');
        $this->addSql('DROP TABLE role_project');
        $this->addSql('DROP TABLE skill');
    }
}
