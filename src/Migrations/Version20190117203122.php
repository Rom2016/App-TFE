<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190117203122 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_results ADD solution_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_results ADD CONSTRAINT FK_8E8131641C0BE183 FOREIGN KEY (solution_id) REFERENCES solution (id)');
        $this->addSql('CREATE INDEX IDX_8E8131641C0BE183 ON audit_results (solution_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_results DROP FOREIGN KEY FK_8E8131641C0BE183');
        $this->addSql('DROP INDEX IDX_8E8131641C0BE183 ON audit_results');
        $this->addSql('ALTER TABLE audit_results DROP solution_id');
    }
}
