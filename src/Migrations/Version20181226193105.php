<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181226193105 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_tests DROP FOREIGN KEY FK_152B98A9727ACA70');
        $this->addSql('DROP INDEX IDX_152B98A9727ACA70 ON audit_tests');
        $this->addSql('ALTER TABLE audit_tests DROP parent_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_tests ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_tests ADD CONSTRAINT FK_152B98A9727ACA70 FOREIGN KEY (parent_id) REFERENCES audit_tests (id)');
        $this->addSql('CREATE INDEX IDX_152B98A9727ACA70 ON audit_tests (parent_id)');
    }
}
