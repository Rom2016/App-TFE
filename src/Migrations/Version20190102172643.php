<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190102172643 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audit_results (id INT AUTO_INCREMENT NOT NULL, audit_id INT NOT NULL, test_id INT NOT NULL, comment LONGTEXT DEFAULT NULL, result VARCHAR(255) NOT NULL, INDEX IDX_8E813164BD29F359 (audit_id), INDEX IDX_8E8131641E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_results ADD CONSTRAINT FK_8E813164BD29F359 FOREIGN KEY (audit_id) REFERENCES int_audit (id)');
        $this->addSql('ALTER TABLE audit_results ADD CONSTRAINT FK_8E8131641E5D0459 FOREIGN KEY (test_id) REFERENCES audit_tests (id)');
        $this->addSql('DROP TABLE audit_company_result');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audit_company_result (id INT AUTO_INCREMENT NOT NULL, test_id INT DEFAULT NULL, information LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, selection VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, selected TINYINT(1) DEFAULT NULL, done TINYINT(1) DEFAULT NULL, passed TINYINT(1) NOT NULL, note LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_D3CAB44F1E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_company_result ADD CONSTRAINT FK_D3CAB44F1E5D0459 FOREIGN KEY (test_id) REFERENCES audit_test_phase (id)');
        $this->addSql('DROP TABLE audit_results');
    }
}
