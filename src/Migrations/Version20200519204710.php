<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519204710 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_29D6873E57B8F0DE');
        $this->addSql('CREATE TEMPORARY TABLE __temp__offer AS SELECT id, auction_id, price, type, created_at, updated_at FROM offer');
        $this->addSql('DROP TABLE offer');
        $this->addSql('CREATE TABLE offer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, auction_id INTEGER DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, type VARCHAR(10) NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT FK_29D6873E57B8F0DE FOREIGN KEY (auction_id) REFERENCES auctions (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO offer (id, auction_id, price, type, created_at, updated_at) SELECT id, auction_id, price, type, created_at, updated_at FROM __temp__offer');
        $this->addSql('DROP TABLE __temp__offer');
        $this->addSql('CREATE INDEX IDX_29D6873E57B8F0DE ON offer (auction_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__auctions AS SELECT id, title, description, price, starting_price, created_at, updated_at, expires_at, status FROM auctions');
        $this->addSql('DROP TABLE auctions');
        $this->addSql('CREATE TABLE auctions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, description VARCHAR(255) NOT NULL COLLATE BINARY, price NUMERIC(10, 2) NOT NULL, starting_price NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, status VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_72D6E9007E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO auctions (id, title, description, price, starting_price, created_at, updated_at, expires_at, status) SELECT id, title, description, price, starting_price, created_at, updated_at, expires_at, status FROM __temp__auctions');
        $this->addSql('DROP TABLE __temp__auctions');
        $this->addSql('CREATE INDEX IDX_72D6E9007E3C61F9 ON auctions (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_72D6E9007E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__auctions AS SELECT id, title, description, price, starting_price, created_at, updated_at, expires_at, status FROM auctions');
        $this->addSql('DROP TABLE auctions');
        $this->addSql('CREATE TABLE auctions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, starting_price NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, status VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO auctions (id, title, description, price, starting_price, created_at, updated_at, expires_at, status) SELECT id, title, description, price, starting_price, created_at, updated_at, expires_at, status FROM __temp__auctions');
        $this->addSql('DROP TABLE __temp__auctions');
        $this->addSql('DROP INDEX IDX_29D6873E57B8F0DE');
        $this->addSql('CREATE TEMPORARY TABLE __temp__offer AS SELECT id, auction_id, price, type, created_at, updated_at FROM offer');
        $this->addSql('DROP TABLE offer');
        $this->addSql('CREATE TABLE offer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, auction_id INTEGER DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, type VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO offer (id, auction_id, price, type, created_at, updated_at) SELECT id, auction_id, price, type, created_at, updated_at FROM __temp__offer');
        $this->addSql('DROP TABLE __temp__offer');
        $this->addSql('CREATE INDEX IDX_29D6873E57B8F0DE ON offer (auction_id)');
    }
}
