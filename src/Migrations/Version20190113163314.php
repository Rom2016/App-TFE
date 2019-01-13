<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190113163314 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE link_snap_test (id INT AUTO_INCREMENT NOT NULL, snap_id INT NOT NULL, test_id INT NOT NULL, INDEX IDX_6DA83AD9247F99A1 (snap_id), INDEX IDX_6DA83AD91E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snapshot (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, date_creation DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_snap_test ADD CONSTRAINT FK_6DA83AD9247F99A1 FOREIGN KEY (snap_id) REFERENCES snapshot (id)');
        $this->addSql('ALTER TABLE link_snap_test ADD CONSTRAINT FK_6DA83AD91E5D0459 FOREIGN KEY (test_id) REFERENCES audit_tests (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE link_snap_test DROP FOREIGN KEY FK_6DA83AD9247F99A1');
        $this->addSql('DROP TABLE link_snap_test');
        $this->addSql('DROP TABLE snapshot');
    }
}
