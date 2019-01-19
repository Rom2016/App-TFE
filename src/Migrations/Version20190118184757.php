<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190118184757 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_permission ADD audit_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446BD29F359 FOREIGN KEY (audit_id) REFERENCES int_audit (id)');
        $this->addSql('CREATE INDEX IDX_472E5446BD29F359 ON user_permission (audit_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446BD29F359');
        $this->addSql('DROP INDEX IDX_472E5446BD29F359 ON user_permission');
        $this->addSql('ALTER TABLE user_permission DROP audit_id');
    }
}
