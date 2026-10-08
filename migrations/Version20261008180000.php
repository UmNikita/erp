<?php

declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261008180000 extends AbstractMigration
{
    public function getDescription(): string { return 'Persist recipient read state for manager feedback'; }
    public function up(Schema $schema): void { $this->addSql('ALTER TABLE task_feedback ADD read_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL'); }
    public function down(Schema $schema): void { $this->addSql('ALTER TABLE task_feedback DROP read_at'); }
}
