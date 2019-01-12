<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190112152720 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE link_selec_infra (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_select_infra ADD test_id INT NOT NULL');
        $this->addSql('ALTER TABLE link_select_infra ADD CONSTRAINT FK_B9A48EA01E5D0459 FOREIGN KEY (test_id) REFERENCES audit_tests (id)');
        $this->addSql('CREATE INDEX IDX_B9A48EA01E5D0459 ON link_select_infra (test_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE link_selec_infra');
        $this->addSql('ALTER TABLE link_select_infra DROP FOREIGN KEY FK_B9A48EA01E5D0459');
        $this->addSql('DROP INDEX IDX_B9A48EA01E5D0459 ON link_select_infra');
        $this->addSql('ALTER TABLE link_select_infra DROP test_id');
    }
}
