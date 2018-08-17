<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180817090033 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE solution_company_size DROP FOREIGN KEY FK_A46FEEF81C0BE183');
        $this->addSql('CREATE TABLE product_company_size (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, size_id INT DEFAULT NULL, test_id INT DEFAULT NULL, INDEX IDX_39091CEF4584665A (product_id), INDEX IDX_39091CEF498DA827 (size_id), INDEX IDX_39091CEF1E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_company_size ADD CONSTRAINT FK_39091CEF4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_company_size ADD CONSTRAINT FK_39091CEF498DA827 FOREIGN KEY (size_id) REFERENCES company_size (id)');
        $this->addSql('ALTER TABLE product_company_size ADD CONSTRAINT FK_39091CEF1E5D0459 FOREIGN KEY (test_id) REFERENCES audit_test_phase (id)');
        $this->addSql('DROP TABLE solution');
        $this->addSql('DROP TABLE solution_company_size');
        $this->addSql('DROP TABLE solution_description');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE solution (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, cost VARCHAR(100) DEFAULT NULL COLLATE utf8mb4_unicode_ci, pic VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, link VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE solution_company_size (id INT AUTO_INCREMENT NOT NULL, solution_id INT DEFAULT NULL, size_id INT DEFAULT NULL, test_id INT DEFAULT NULL, INDEX IDX_A46FEEF81C0BE183 (solution_id), INDEX IDX_A46FEEF8498DA827 (size_id), INDEX IDX_A46FEEF81E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE solution_description (id INT AUTO_INCREMENT NOT NULL, header VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, description LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, pros LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, con LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE solution_company_size ADD CONSTRAINT FK_A46FEEF81C0BE183 FOREIGN KEY (solution_id) REFERENCES solution (id)');
        $this->addSql('ALTER TABLE solution_company_size ADD CONSTRAINT FK_A46FEEF81E5D0459 FOREIGN KEY (test_id) REFERENCES audit_test_phase (id)');
        $this->addSql('ALTER TABLE solution_company_size ADD CONSTRAINT FK_A46FEEF8498DA827 FOREIGN KEY (size_id) REFERENCES company_size (id)');
        $this->addSql('DROP TABLE product_company_size');
    }
}
