<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190112200054 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE infra_customert (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE infra_customer DROP FOREIGN KEY FK_11D14D139395C3F3');
        $this->addSql('DROP INDEX IDX_11D14D139395C3F3 ON infra_customer');
        $this->addSql('ALTER TABLE infra_customer CHANGE customer_id audit_id INT NOT NULL');
        $this->addSql('ALTER TABLE infra_customer ADD CONSTRAINT FK_11D14D13BD29F359 FOREIGN KEY (audit_id) REFERENCES int_audit (id)');
        $this->addSql('CREATE INDEX IDX_11D14D13BD29F359 ON infra_customer (audit_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE infra_customert');
        $this->addSql('ALTER TABLE infra_customer DROP FOREIGN KEY FK_11D14D13BD29F359');
        $this->addSql('DROP INDEX IDX_11D14D13BD29F359 ON infra_customer');
        $this->addSql('ALTER TABLE infra_customer CHANGE audit_id customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE infra_customer ADD CONSTRAINT FK_11D14D139395C3F3 FOREIGN KEY (customer_id) REFERENCES int_customer (id)');
        $this->addSql('CREATE INDEX IDX_11D14D139395C3F3 ON infra_customer (customer_id)');
    }
}
