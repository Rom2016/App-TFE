<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190109193801 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_results ADD result_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_results ADD CONSTRAINT FK_8E8131647A7B643 FOREIGN KEY (result_id) REFERENCES test_status (id)');
        $this->addSql('CREATE INDEX IDX_8E8131647A7B643 ON audit_results (result_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_results DROP FOREIGN KEY FK_8E8131647A7B643');
        $this->addSql('DROP INDEX IDX_8E8131647A7B643 ON audit_results');
        $this->addSql('ALTER TABLE audit_results DROP result_id');
    }
}
