<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181229145248 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE link_tests_infra (id INT AUTO_INCREMENT NOT NULL, infra_id INT NOT NULL, test_id INT NOT NULL, action TINYINT(1) NOT NULL, INDEX IDX_3A63AB30362A80ED (infra_id), INDEX IDX_3A63AB301E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_tests_infra ADD CONSTRAINT FK_3A63AB30362A80ED FOREIGN KEY (infra_id) REFERENCES audit_tests_infra (id)');
        $this->addSql('ALTER TABLE link_tests_infra ADD CONSTRAINT FK_3A63AB301E5D0459 FOREIGN KEY (test_id) REFERENCES audit_tests (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE link_tests_infra');
    }
}
