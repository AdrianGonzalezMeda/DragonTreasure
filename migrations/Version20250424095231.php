<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424095231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE dragon_treasure ADD owner_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dragon_treasure ADD CONSTRAINT FK_9E31BF5F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9E31BF5F7E3C61F9 ON dragon_treasure (owner_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "dragon_treasure" DROP CONSTRAINT FK_9E31BF5F7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9E31BF5F7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "dragon_treasure" DROP owner_id
        SQL);
    }
}
