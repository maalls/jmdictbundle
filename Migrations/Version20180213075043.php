<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180213075043 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE kanji_reading DROP FOREIGN KEY FK_B16EDF35527275CD');
        $this->addSql('ALTER TABLE kanji_reading DROP FOREIGN KEY FK_B16EDF35FB3081B8');
        $this->addSql('ALTER TABLE kanji_reading ADD CONSTRAINT FK_B16EDF35527275CD FOREIGN KEY (reading_id) REFERENCES word (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE kanji_reading ADD CONSTRAINT FK_B16EDF35FB3081B8 FOREIGN KEY (kanji_id) REFERENCES word (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sense_source DROP FOREIGN KEY FK_AC5C8D938707C57E');
        $this->addSql('ALTER TABLE sense_source ADD CONSTRAINT FK_AC5C8D938707C57E FOREIGN KEY (sense_id) REFERENCES sense (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sense_word DROP FOREIGN KEY FK_F02E86C78707C57E');
        $this->addSql('ALTER TABLE sense_word DROP FOREIGN KEY FK_F02E86C7E357438D');
        $this->addSql('ALTER TABLE sense_word ADD CONSTRAINT FK_F02E86C78707C57E FOREIGN KEY (sense_id) REFERENCES sense (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sense_word ADD CONSTRAINT FK_F02E86C7E357438D FOREIGN KEY (word_id) REFERENCES word (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sense_glossary DROP FOREIGN KEY FK_BD4898498707C57E');
        $this->addSql('ALTER TABLE sense_glossary ADD CONSTRAINT FK_BD4898498707C57E FOREIGN KEY (sense_id) REFERENCES sense (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sense_reference DROP FOREIGN KEY FK_4E7B6D9E8707C57E');
        $this->addSql('ALTER TABLE sense_reference DROP FOREIGN KEY FK_4E7B6D9EE357438D');
        $this->addSql('ALTER TABLE sense_reference ADD CONSTRAINT FK_4E7B6D9E8707C57E FOREIGN KEY (sense_id) REFERENCES sense (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sense_reference ADD CONSTRAINT FK_4E7B6D9EE357438D FOREIGN KEY (word_id) REFERENCES word (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE kanji_reading DROP FOREIGN KEY FK_B16EDF35FB3081B8');
        $this->addSql('ALTER TABLE kanji_reading DROP FOREIGN KEY FK_B16EDF35527275CD');
        $this->addSql('ALTER TABLE kanji_reading ADD CONSTRAINT FK_B16EDF35FB3081B8 FOREIGN KEY (kanji_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE kanji_reading ADD CONSTRAINT FK_B16EDF35527275CD FOREIGN KEY (reading_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE sense_glossary DROP FOREIGN KEY FK_BD4898498707C57E');
        $this->addSql('ALTER TABLE sense_glossary ADD CONSTRAINT FK_BD4898498707C57E FOREIGN KEY (sense_id) REFERENCES sense (id)');
        $this->addSql('ALTER TABLE sense_reference DROP FOREIGN KEY FK_4E7B6D9E8707C57E');
        $this->addSql('ALTER TABLE sense_reference DROP FOREIGN KEY FK_4E7B6D9EE357438D');
        $this->addSql('ALTER TABLE sense_reference ADD CONSTRAINT FK_4E7B6D9E8707C57E FOREIGN KEY (sense_id) REFERENCES sense (id)');
        $this->addSql('ALTER TABLE sense_reference ADD CONSTRAINT FK_4E7B6D9EE357438D FOREIGN KEY (word_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE sense_source DROP FOREIGN KEY FK_AC5C8D938707C57E');
        $this->addSql('ALTER TABLE sense_source ADD CONSTRAINT FK_AC5C8D938707C57E FOREIGN KEY (sense_id) REFERENCES sense (id)');
        $this->addSql('ALTER TABLE sense_word DROP FOREIGN KEY FK_F02E86C78707C57E');
        $this->addSql('ALTER TABLE sense_word DROP FOREIGN KEY FK_F02E86C7E357438D');
        $this->addSql('ALTER TABLE sense_word ADD CONSTRAINT FK_F02E86C78707C57E FOREIGN KEY (sense_id) REFERENCES sense (id)');
        $this->addSql('ALTER TABLE sense_word ADD CONSTRAINT FK_F02E86C7E357438D FOREIGN KEY (word_id) REFERENCES word (id)');
    }
}
