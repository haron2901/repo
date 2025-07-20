<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250719234133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task_queue (id SERIAL NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B85964437E3C61F9 ON task_queue (owner_id)');
        $this->addSql('COMMENT ON COLUMN task_queue.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE task_queue ADD CONSTRAINT FK_B85964437E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD queue_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25477B5BAE FOREIGN KEY (queue_id) REFERENCES task_queue (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_527EDB25477B5BAE ON task (queue_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25477B5BAE');
        $this->addSql('ALTER TABLE task_queue DROP CONSTRAINT FK_B85964437E3C61F9');
        $this->addSql('DROP TABLE task_queue');
        $this->addSql('DROP INDEX IDX_527EDB25477B5BAE');
        $this->addSql('ALTER TABLE task DROP queue_id');
    }
}
