<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180709195653 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_test_phase ADD id_phase_id INT NOT NULL');
        $this->addSql('ALTER TABLE audit_test_phase ADD CONSTRAINT FK_C7BD683FC9D2FD1D FOREIGN KEY (id_phase_id) REFERENCES audit_phase (id)');
        $this->addSql('CREATE INDEX IDX_C7BD683FC9D2FD1D ON audit_test_phase (id_phase_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_test_phase DROP FOREIGN KEY FK_C7BD683FC9D2FD1D');
        $this->addSql('DROP INDEX IDX_C7BD683FC9D2FD1D ON audit_test_phase');
        $this->addSql('ALTER TABLE audit_test_phase DROP id_phase_id');
    }
}
