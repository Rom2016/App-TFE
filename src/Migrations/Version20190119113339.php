<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190119113339 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE int_audit ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE int_audit ADD CONSTRAINT FK_626DD7A6727ACA70 FOREIGN KEY (parent_id) REFERENCES int_audit (id)');
        $this->addSql('CREATE INDEX IDX_626DD7A6727ACA70 ON int_audit (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE int_audit DROP FOREIGN KEY FK_626DD7A6727ACA70');
        $this->addSql('DROP INDEX IDX_626DD7A6727ACA70 ON int_audit');
        $this->addSql('ALTER TABLE int_audit DROP parent_id');
    }
}
