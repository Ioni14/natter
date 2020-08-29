<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200829144844 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add space and message tables.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE space_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, space_id INT NOT NULL, author VARCHAR(30) NOT NULL, text VARCHAR(1024) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307F23575340 ON message (space_id)');
        $this->addSql('COMMENT ON COLUMN message.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE space (id INT NOT NULL, name VARCHAR(255) NOT NULL, owner VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F23575340 FOREIGN KEY (space_id) REFERENCES space (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F23575340');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE space_id_seq CASCADE');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE space');
    }
}
