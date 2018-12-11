<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181210171932 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audit_sub_section (id INT AUTO_INCREMENT NOT NULL, section_id INT DEFAULT NULL, subsection VARCHAR(255) NOT NULL, INDEX IDX_7FD4178ED823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_sub_section ADD CONSTRAINT FK_7FD4178ED823E37A FOREIGN KEY (section_id) REFERENCES audit_phase (id)');
        $this->addSql('ALTER TABLE audit_test_phase ADD subsection_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_test_phase ADD CONSTRAINT FK_C7BD683F87B204D9 FOREIGN KEY (subsection_id) REFERENCES audit_sub_section (id)');
        $this->addSql('CREATE INDEX IDX_C7BD683F87B204D9 ON audit_test_phase (subsection_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_test_phase DROP FOREIGN KEY FK_C7BD683F87B204D9');
        $this->addSql('DROP TABLE audit_sub_section');
        $this->addSql('DROP INDEX IDX_C7BD683F87B204D9 ON audit_test_phase');
        $this->addSql('ALTER TABLE audit_test_phase DROP subsection_id');
    }
}
