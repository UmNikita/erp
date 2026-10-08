<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'task_overdue_report')]
class TaskOverdueReport
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TaskItem::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private TaskItem $task;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $author;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $assigneeAtReport = null;

    #[ORM\Column(type: Types::JSON)]
    private array $assigneeAtReportIds = [];

    #[ORM\Column(length: 1000)]
    private string $reason;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $previousDueAt;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $newDueAt;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct(TaskItem $task, ?User $author, string $reason, \DateTimeImmutable $previousDueAt, \DateTimeImmutable $newDueAt)
    {
        $this->task = $task;
        $this->author = $author;
        $this->assigneeAtReport = $task->getAssignee();
        $this->assigneeAtReportIds = $task->getAssigneeIds();
        $this->reason = $reason;
        $this->previousDueAt = $previousDueAt;
        $this->newDueAt = $newDueAt;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTask(): TaskItem { return $this->task; }
    public function getAuthor(): ?User { return $this->author; }
    public function getAssigneeAtReport(): ?User { return $this->assigneeAtReport; }
    public function getAssigneeAtReportIds(): array { return $this->assigneeAtReportIds ?: ($this->assigneeAtReport?->getId() ? [$this->assigneeAtReport->getId()] : []); }
    public function getReason(): string { return $this->reason; }
    public function getPreviousDueAt(): \DateTimeImmutable { return $this->previousDueAt; }
    public function getNewDueAt(): \DateTimeImmutable { return $this->newDueAt; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
