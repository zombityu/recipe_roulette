<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230623162518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receipt ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B645C54C8C93 FOREIGN KEY (type_id) REFERENCES receipt_type (id)');
        $this->addSql('CREATE INDEX IDX_5399B645C54C8C93 ON receipt (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receipt DROP FOREIGN KEY FK_5399B645C54C8C93');
        $this->addSql('DROP INDEX IDX_5399B645C54C8C93 ON receipt');
        $this->addSql('ALTER TABLE receipt DROP type_id');
    }
}
