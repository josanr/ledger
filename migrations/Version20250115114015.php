<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115114015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE TABLE ledger
            (
                id          UUID                        NOT NULL,
                code        VARCHAR(255)                NOT NULL,
                name        VARCHAR(100)                NOT NULL,
                description TEXT                        NOT NULL,
                ledger_type VARCHAR(50)                 NOT NULL,
                currency_id VARCHAR(3)                  NOT NULL,
                created_at  TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                updated_at  TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                PRIMARY KEY (id)
            )'
        );
        $this->addSql('create unique index ledger__index_code on ledger (code)');

        $this->addSql('COMMENT ON COLUMN ledger.id IS \'(DC2Type:uuid)\'');
        $this->addSql('
            CREATE TABLE transaction
            (
                id               UUID                        NOT NULL,
                ledger_id_id     UUID                        NOT NULL,
                description      TEXT                        NOT NULL,
                transaction_date TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                amount           INT                         NOT NULL,
                currency_id      varchar(3)                  NOT NULL,
                direction        VARCHAR(10)                 NOT NULL,
                created_at       TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                updated_at       TIMESTAMP(0) WITH TIME ZONE NOT NULL,
                PRIMARY KEY (id)
            )
        ');
        $this->addSql('CREATE INDEX IDX_723705D13042294E ON transaction (ledger_id_id)');
        $this->addSql('COMMENT ON COLUMN transaction.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.ledger_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D13042294E FOREIGN KEY (ledger_id_id) REFERENCES ledger (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D13042294E');
        $this->addSql('DROP TABLE ledger');
        $this->addSql('DROP TABLE transaction');
    }
}
