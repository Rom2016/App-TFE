<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180401163904 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD fisrt_name VARCHAR(50) NOT NULL, ADD second_name VARCHAR(50) NOT NULL, DROP prenom, DROP nom, DROP date_creation, CHANGE mdp pass VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD prenom VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, ADD nom VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, ADD date_creation DATE DEFAULT NULL, DROP fisrt_name, DROP second_name, CHANGE pass mdp VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
