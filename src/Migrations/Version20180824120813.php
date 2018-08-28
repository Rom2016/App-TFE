<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180824120813 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audit_company_result (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, test_id INT DEFAULT NULL, information LONGTEXT DEFAULT NULL, selection VARCHAR(255) DEFAULT NULL, INDEX IDX_D3CAB44F979B1AD6 (company_id), INDEX IDX_D3CAB44F1E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_company_result ADD CONSTRAINT FK_D3CAB44F979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE audit_company_result ADD CONSTRAINT FK_D3CAB44F1E5D0459 FOREIGN KEY (test_id) REFERENCES audit_test_phase (id)');
        $this->addSql('DROP TABLE audit_company');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audit_company (id INT AUTO_INCREMENT NOT NULL, id_company_id INT NOT NULL, id_test_id INT NOT NULL, information VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, selected TINYINT(1) NOT NULL, INDEX IDX_5E9DDC3FC0C0AD29 (id_test_id), INDEX IDX_5E9DDC3F32119A01 (id_company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_company ADD CONSTRAINT FK_5E9DDC3F32119A01 FOREIGN KEY (id_company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE audit_company ADD CONSTRAINT FK_5E9DDC3FC0C0AD29 FOREIGN KEY (id_test_id) REFERENCES audit_test_phase (id)');
        $this->addSql('DROP TABLE audit_company_result');
    }
}
