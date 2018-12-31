<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181230174847 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE infra_selection ADD test_id INT DEFAULT NULL, ADD status TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE infra_selection ADD CONSTRAINT FK_6B571D6E1E5D0459 FOREIGN KEY (test_id) REFERENCES audit_tests (id)');
        $this->addSql('CREATE INDEX IDX_6B571D6E1E5D0459 ON infra_selection (test_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE infra_selection DROP FOREIGN KEY FK_6B571D6E1E5D0459');
        $this->addSql('DROP INDEX IDX_6B571D6E1E5D0459 ON infra_selection');
        $this->addSql('ALTER TABLE infra_selection DROP test_id, DROP status');
    }
}
