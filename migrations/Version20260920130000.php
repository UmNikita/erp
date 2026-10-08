<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260920130000 extends AbstractMigration
{
    public function getDescription(): string { return 'Add editable task requester while preserving the actual creator'; }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task_item ADD requested_by_id INT DEFAULT NULL');
        $this->addSql('UPDATE task_item SET requested_by_id = created_by_id WHERE created_by_id IS NOT NULL');
        $this->addSql('CREATE INDEX IDX_TASK_ITEM_REQUESTED_BY ON task_item (requested_by_id)');
        $this->addSql('ALTER TABLE task_item ADD CONSTRAINT FK_TASK_ITEM_REQUESTED_BY FOREIGN KEY (requested_by_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task_item DROP CONSTRAINT FK_TASK_ITEM_REQUESTED_BY');
        $this->addSql('DROP INDEX IDX_TASK_ITEM_REQUESTED_BY');
        $this->addSql('ALTER TABLE task_item DROP requested_by_id');
    }
}
