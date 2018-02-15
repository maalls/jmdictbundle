<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180213101155 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sense_reference ADD related_sense_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sense_reference ADD CONSTRAINT FK_4E7B6D9E21848BD3 FOREIGN KEY (related_sense_id) REFERENCES sense (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4E7B6D9E21848BD3 ON sense_reference (related_sense_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sense_reference DROP FOREIGN KEY FK_4E7B6D9E21848BD3');
        $this->addSql('DROP INDEX IDX_4E7B6D9E21848BD3 ON sense_reference');
        $this->addSql('ALTER TABLE sense_reference DROP related_sense_id');
    }
}
