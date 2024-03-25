<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240325193137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course_document ALTER content TYPE TEXT');
        $this->addSql('ALTER TABLE question ADD answer_id INT NOT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EAA334807 FOREIGN KEY (answer_id) REFERENCES answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6F7494EAA334807 ON question (answer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE course_document ALTER content TYPE TEXT');
        $this->addSql('ALTER TABLE course_document ALTER content TYPE TEXT');
        $this->addSql('ALTER TABLE question DROP CONSTRAINT FK_B6F7494EAA334807');
        $this->addSql('DROP INDEX UNIQ_B6F7494EAA334807');
        $this->addSql('ALTER TABLE question DROP answer_id');
    }
}
