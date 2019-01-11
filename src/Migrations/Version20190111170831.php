<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190111170831 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE logs_administration (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, recipient_id INT NOT NULL, log VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, INDEX IDX_715750D261220EA6 (creator_id), INDEX IDX_715750D2E92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs_type (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE logs_administration ADD CONSTRAINT FK_715750D261220EA6 FOREIGN KEY (creator_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE logs_administration ADD CONSTRAINT FK_715750D2E92F8F78 FOREIGN KEY (recipient_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE logs_administration');
        $this->addSql('DROP TABLE logs_type');
    }
}
