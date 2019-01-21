<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190121130806 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE log_audits (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, audit_id INT NOT NULL, action_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_27BF4DCE953C1C61 (source_id), INDEX IDX_27BF4DCEBD29F359 (audit_id), INDEX IDX_27BF4DCE9D32F035 (action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log_audits ADD CONSTRAINT FK_27BF4DCE953C1C61 FOREIGN KEY (source_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE log_audits ADD CONSTRAINT FK_27BF4DCEBD29F359 FOREIGN KEY (audit_id) REFERENCES int_audit (id)');
        $this->addSql('ALTER TABLE log_audits ADD CONSTRAINT FK_27BF4DCE9D32F035 FOREIGN KEY (action_id) REFERENCES log_action (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE log_audits');
    }
}
