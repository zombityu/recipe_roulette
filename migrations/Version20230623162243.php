<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230623162243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receipt_type DROP FOREIGN KEY FK_1AA56CA52B5CA896');
        $this->addSql('DROP INDEX IDX_1AA56CA52B5CA896 ON receipt_type');
        $this->addSql('ALTER TABLE receipt_type DROP receipt_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receipt_type ADD receipt_id INT NOT NULL');
        $this->addSql('ALTER TABLE receipt_type ADD CONSTRAINT FK_1AA56CA52B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1AA56CA52B5CA896 ON receipt_type (receipt_id)');
    }
}
