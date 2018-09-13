<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180913140421 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_company DROP FOREIGN KEY FK_5E9DDC3FA76ED395');
        $this->addSql('DROP INDEX IDX_5E9DDC3FA76ED395 ON audit_company');
        $this->addSql('ALTER TABLE audit_company CHANGE user_id owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_company ADD CONSTRAINT FK_5E9DDC3F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES app_user (id)');
        $this->addSql('CREATE INDEX IDX_5E9DDC3F7E3C61F9 ON audit_company (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_company DROP FOREIGN KEY FK_5E9DDC3F7E3C61F9');
        $this->addSql('DROP INDEX IDX_5E9DDC3F7E3C61F9 ON audit_company');
        $this->addSql('ALTER TABLE audit_company CHANGE owner_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_company ADD CONSTRAINT FK_5E9DDC3FA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id)');
        $this->addSql('CREATE INDEX IDX_5E9DDC3FA76ED395 ON audit_company (user_id)');
    }
}
