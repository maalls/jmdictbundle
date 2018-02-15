<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180214011822 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sense ADD base_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sense ADD CONSTRAINT FK_F2B33FB6967DF41 FOREIGN KEY (base_id) REFERENCES base (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_F2B33FB6967DF41 ON sense (base_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sense DROP FOREIGN KEY FK_F2B33FB6967DF41');
        $this->addSql('DROP INDEX IDX_F2B33FB6967DF41 ON sense');
        $this->addSql('ALTER TABLE sense DROP base_id');
    }
}
