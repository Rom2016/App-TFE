<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190112151739 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE infra_selection ADD date_creation DATETIME DEFAULT NULL, ADD date_archive DATETIME NOT NULL, DROP test_id, DROP status, DROP action');
        $this->addSql('ALTER TABLE link_select_infra ADD action TINYINT(1) NOT NULL, ADD date_creation DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE infra_selection ADD test_id INT DEFAULT NULL, ADD status TINYINT(1) DEFAULT NULL, ADD action TINYINT(1) DEFAULT NULL, DROP date_creation, DROP date_archive');
        $this->addSql('ALTER TABLE link_select_infra DROP action, DROP date_creation');
    }
}
