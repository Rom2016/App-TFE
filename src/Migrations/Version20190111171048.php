<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190111171048 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE logs_administration ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE logs_administration ADD CONSTRAINT FK_715750D2C54C8C93 FOREIGN KEY (type_id) REFERENCES logs_type (id)');
        $this->addSql('CREATE INDEX IDX_715750D2C54C8C93 ON logs_administration (type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE logs_administration DROP FOREIGN KEY FK_715750D2C54C8C93');
        $this->addSql('DROP INDEX IDX_715750D2C54C8C93 ON logs_administration');
        $this->addSql('ALTER TABLE logs_administration DROP type_id');
    }
}
