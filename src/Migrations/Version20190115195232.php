<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190115195232 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_results DROP aqaq');
        $this->addSql('ALTER TABLE link_snap_pre ADD pre_id INT NOT NULL');
        $this->addSql('ALTER TABLE link_snap_pre ADD CONSTRAINT FK_8CEADB5CAC0A04DE FOREIGN KEY (pre_id) REFERENCES audit_tests_infra (id)');
        $this->addSql('CREATE INDEX IDX_8CEADB5CAC0A04DE ON link_snap_pre (pre_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_results ADD aqaq INT DEFAULT NULL');
        $this->addSql('ALTER TABLE link_snap_pre DROP FOREIGN KEY FK_8CEADB5CAC0A04DE');
        $this->addSql('DROP INDEX IDX_8CEADB5CAC0A04DE ON link_snap_pre');
        $this->addSql('ALTER TABLE link_snap_pre DROP pre_id');
    }
}
