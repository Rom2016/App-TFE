<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180722173711 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_test_phase ADD id_parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_test_phase ADD CONSTRAINT FK_C7BD683FF24F7657 FOREIGN KEY (id_parent_id) REFERENCES audit_test_phase (id)');
        $this->addSql('CREATE INDEX IDX_C7BD683FF24F7657 ON audit_test_phase (id_parent_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_test_phase DROP FOREIGN KEY FK_C7BD683FF24F7657');
        $this->addSql('DROP INDEX IDX_C7BD683FF24F7657 ON audit_test_phase');
        $this->addSql('ALTER TABLE audit_test_phase DROP id_parent_id');
    }
}
