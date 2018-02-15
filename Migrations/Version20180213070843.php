<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180213070843 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE kanji_reading (id INT AUTO_INCREMENT NOT NULL, kanji_id INT DEFAULT NULL, reading_id INT DEFAULT NULL, INDEX IDX_B16EDF35FB3081B8 (kanji_id), INDEX IDX_B16EDF35527275CD (reading_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sense_source (id INT AUTO_INCREMENT NOT NULL, sense_id INT DEFAULT NULL, source VARCHAR(64) NOT NULL, INDEX IDX_AC5C8D938707C57E (sense_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sense_word (id INT AUTO_INCREMENT NOT NULL, sense_id INT DEFAULT NULL, word_id INT DEFAULT NULL, INDEX IDX_F02E86C78707C57E (sense_id), INDEX IDX_F02E86C7E357438D (word_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sense_glossary (id INT AUTO_INCREMENT NOT NULL, sense_id INT DEFAULT NULL, glossary VARCHAR(64) NOT NULL, INDEX IDX_BD4898498707C57E (sense_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sense (id INT AUTO_INCREMENT NOT NULL, pos LONGTEXT NOT NULL, field VARCHAR(32) NOT NULL, misc LONGTEXT NOT NULL, dial LONGTEXT NOT NULL, info LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE base (id INT NOT NULL, value VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE word (id INT AUTO_INCREMENT NOT NULL, base_id INT DEFAULT NULL, type VARCHAR(8) NOT NULL, info LONGTEXT NOT NULL, no_kanji TINYINT(1) NOT NULL, news_level SMALLINT NOT NULL, ichi_level SMALLINT NOT NULL, spec_level SMALLINT NOT NULL, gai_level SMALLINT NOT NULL, frequency_level SMALLINT NOT NULL, INDEX IDX_C3F175116967DF41 (base_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sense_reference (id INT AUTO_INCREMENT NOT NULL, sense_id INT DEFAULT NULL, word_id INT DEFAULT NULL, type VARCHAR(16) NOT NULL, INDEX IDX_4E7B6D9E8707C57E (sense_id), INDEX IDX_4E7B6D9EE357438D (word_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE kanji_reading ADD CONSTRAINT FK_B16EDF35FB3081B8 FOREIGN KEY (kanji_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE kanji_reading ADD CONSTRAINT FK_B16EDF35527275CD FOREIGN KEY (reading_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE sense_source ADD CONSTRAINT FK_AC5C8D938707C57E FOREIGN KEY (sense_id) REFERENCES sense (id)');
        $this->addSql('ALTER TABLE sense_word ADD CONSTRAINT FK_F02E86C78707C57E FOREIGN KEY (sense_id) REFERENCES sense (id)');
        $this->addSql('ALTER TABLE sense_word ADD CONSTRAINT FK_F02E86C7E357438D FOREIGN KEY (word_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE sense_glossary ADD CONSTRAINT FK_BD4898498707C57E FOREIGN KEY (sense_id) REFERENCES sense (id)');
        $this->addSql('ALTER TABLE word ADD CONSTRAINT FK_C3F175116967DF41 FOREIGN KEY (base_id) REFERENCES base (id)');
        $this->addSql('ALTER TABLE sense_reference ADD CONSTRAINT FK_4E7B6D9E8707C57E FOREIGN KEY (sense_id) REFERENCES sense (id)');
        $this->addSql('ALTER TABLE sense_reference ADD CONSTRAINT FK_4E7B6D9EE357438D FOREIGN KEY (word_id) REFERENCES word (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sense_source DROP FOREIGN KEY FK_AC5C8D938707C57E');
        $this->addSql('ALTER TABLE sense_word DROP FOREIGN KEY FK_F02E86C78707C57E');
        $this->addSql('ALTER TABLE sense_glossary DROP FOREIGN KEY FK_BD4898498707C57E');
        $this->addSql('ALTER TABLE sense_reference DROP FOREIGN KEY FK_4E7B6D9E8707C57E');
        $this->addSql('ALTER TABLE word DROP FOREIGN KEY FK_C3F175116967DF41');
        $this->addSql('ALTER TABLE kanji_reading DROP FOREIGN KEY FK_B16EDF35FB3081B8');
        $this->addSql('ALTER TABLE kanji_reading DROP FOREIGN KEY FK_B16EDF35527275CD');
        $this->addSql('ALTER TABLE sense_word DROP FOREIGN KEY FK_F02E86C7E357438D');
        $this->addSql('ALTER TABLE sense_reference DROP FOREIGN KEY FK_4E7B6D9EE357438D');
        $this->addSql('DROP TABLE kanji_reading');
        $this->addSql('DROP TABLE sense_source');
        $this->addSql('DROP TABLE sense_word');
        $this->addSql('DROP TABLE sense_glossary');
        $this->addSql('DROP TABLE sense');
        $this->addSql('DROP TABLE base');
        $this->addSql('DROP TABLE word');
        $this->addSql('DROP TABLE sense_reference');
    }
}
