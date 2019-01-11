<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190111173630 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_user DROP FOREIGN KEY FK_88BDF3E961220EA6');
        $this->addSql('ALTER TABLE app_user DROP FOREIGN KEY FK_88BDF3E9F703974A');
        $this->addSql('DROP INDEX IDX_88BDF3E961220EA6 ON app_user');
        $this->addSql('DROP INDEX IDX_88BDF3E9F703974A ON app_user');
        $this->addSql('ALTER TABLE app_user DROP creator_id, DROP last_modified_by_id, DROP last_modified_date');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_user ADD creator_id INT DEFAULT NULL, ADD last_modified_by_id INT DEFAULT NULL, ADD last_modified_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E961220EA6 FOREIGN KEY (creator_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E9F703974A FOREIGN KEY (last_modified_by_id) REFERENCES app_user (id)');
        $this->addSql('CREATE INDEX IDX_88BDF3E961220EA6 ON app_user (creator_id)');
        $this->addSql('CREATE INDEX IDX_88BDF3E9F703974A ON app_user (last_modified_by_id)');
    }
}
