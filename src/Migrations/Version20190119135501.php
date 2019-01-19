<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190119135501 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE section_points (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, audit_id INT NOT NULL, point INT NOT NULL, INDEX IDX_6DF27C0FD823E37A (section_id), INDEX IDX_6DF27C0FBD29F359 (audit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE section_points ADD CONSTRAINT FK_6DF27C0FD823E37A FOREIGN KEY (section_id) REFERENCES audit_section (id)');
        $this->addSql('ALTER TABLE section_points ADD CONSTRAINT FK_6DF27C0FBD29F359 FOREIGN KEY (audit_id) REFERENCES int_audit (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE section_points');
    }
}
