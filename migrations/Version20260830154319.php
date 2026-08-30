<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260830154319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add trigram indexes for client search';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS pg_trgm');

        $this->addSql(
            'CREATE INDEX idx_client_name_trgm
             ON client USING GIN (LOWER(name) gin_trgm_ops)'
        );

        $this->addSql(
            'CREATE INDEX idx_client_email_trgm
             ON client USING GIN (LOWER(email) gin_trgm_ops)'
        );

        $this->addSql(
            'CREATE INDEX idx_client_phone_trgm
             ON client USING GIN (phone gin_trgm_ops)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_client_name_trgm');
        $this->addSql('DROP INDEX idx_client_email_trgm');
        $this->addSql('DROP INDEX idx_client_phone_trgm');
    }
}
