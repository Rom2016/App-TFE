<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190113193615 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE link_snap_section (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_results ADD selection_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_results ADD CONSTRAINT FK_8E813164E48EFE78 FOREIGN KEY (selection_id) REFERENCES test_selections (id)');
        $this->addSql('CREATE INDEX IDX_8E813164E48EFE78 ON audit_results (selection_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE link_snap_section');
        $this->addSql('ALTER TABLE audit_results DROP FOREIGN KEY FK_8E813164E48EFE78');
        $this->addSql('DROP INDEX IDX_8E813164E48EFE78 ON audit_results');
        $this->addSql('ALTER TABLE audit_results DROP selection_id');
    }
}
