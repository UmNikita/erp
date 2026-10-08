<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260920150000 extends AbstractMigration
{
    public function getDescription(): string { return 'Snapshot the responsible employee when a task deadline is extended'; }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task_overdue_report ADD assignee_at_report_id INT DEFAULT NULL');
        $this->addSql('UPDATE task_overdue_report r SET assignee_at_report_id = COALESCE(r.author_id, t.assignee_id) FROM task_item t WHERE t.id = r.task_id');
        $this->addSql('CREATE INDEX IDX_TASK_OVERDUE_ASSIGNEE_AT_REPORT ON task_overdue_report (assignee_at_report_id)');
        $this->addSql('ALTER TABLE task_overdue_report ADD CONSTRAINT FK_TASK_OVERDUE_ASSIGNEE_AT_REPORT FOREIGN KEY (assignee_at_report_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task_overdue_report DROP CONSTRAINT FK_TASK_OVERDUE_ASSIGNEE_AT_REPORT');
        $this->addSql('DROP INDEX IDX_TASK_OVERDUE_ASSIGNEE_AT_REPORT');
        $this->addSql('ALTER TABLE task_overdue_report DROP assignee_at_report_id');
    }
}
