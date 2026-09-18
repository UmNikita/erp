<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add recoverable deletion for accounting operations';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE accounting_transaction ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_ACC_TRANSACTION_DELETED_AT ON accounting_transaction (deleted_at)');
        $this->addSql('ALTER TABLE accounting_audit ADD restored_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_ACC_TRANSACTION_DELETED_AT');
        $this->addSql('ALTER TABLE accounting_transaction DROP deleted_at');
        $this->addSql('ALTER TABLE accounting_audit DROP restored_at');
    }
}
