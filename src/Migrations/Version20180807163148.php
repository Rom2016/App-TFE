<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180807163148 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tests_infrastructure (id INT AUTO_INCREMENT NOT NULL, test_infra_id INT DEFAULT NULL, test_phase_id INT DEFAULT NULL, INDEX IDX_C38374EFA3498BBB (test_infra_id), INDEX IDX_C38374EFC6A1ADE (test_phase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tests_infrastructure ADD CONSTRAINT FK_C38374EFA3498BBB FOREIGN KEY (test_infra_id) REFERENCES audit_test_infrastructure (id)');
        $this->addSql('ALTER TABLE tests_infrastructure ADD CONSTRAINT FK_C38374EFC6A1ADE FOREIGN KEY (test_phase_id) REFERENCES audit_test_phase (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tests_infrastructure');
    }
}
