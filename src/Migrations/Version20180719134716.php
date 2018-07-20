<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180719134716 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company RENAME INDEX size_idx TO IDX_4FBF094F498DA827');
        $this->addSql('ALTER TABLE solution ADD id_test_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE solution ADD CONSTRAINT FK_9F3329DBC0C0AD29 FOREIGN KEY (id_test_id) REFERENCES audit_test_phase (id)');
        $this->addSql('CREATE INDEX IDX_9F3329DBC0C0AD29 ON solution (id_test_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company RENAME INDEX idx_4fbf094f498da827 TO size_idx');
        $this->addSql('ALTER TABLE solution DROP FOREIGN KEY FK_9F3329DBC0C0AD29');
        $this->addSql('DROP INDEX IDX_9F3329DBC0C0AD29 ON solution');
        $this->addSql('ALTER TABLE solution DROP id_test_id');
    }
}
