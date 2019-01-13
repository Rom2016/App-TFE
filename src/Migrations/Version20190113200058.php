<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190113200058 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE log_admin_customer (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, customer_id INT NOT NULL, date_creation DATETIME NOT NULL, INDEX IDX_F3C5B85961220EA6 (creator_id), INDEX IDX_F3C5B8599395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log_admin_customer ADD CONSTRAINT FK_F3C5B85961220EA6 FOREIGN KEY (creator_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE log_admin_customer ADD CONSTRAINT FK_F3C5B8599395C3F3 FOREIGN KEY (customer_id) REFERENCES int_customer (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE log_admin_customer');
    }
}
