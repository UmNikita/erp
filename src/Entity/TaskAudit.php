<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'task_audit')]
class TaskAudit
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TaskItem::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?TaskItem $task;

    #[ORM\Column(length: 180)]
    private string $taskTitle;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $actor;

    #[ORM\Column(length: 32)]
    private string $action;

    #[ORM\Column(type: Types::JSON)]
    private array $changes;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct(TaskItem $task, ?User $actor, string $action, array $changes = [])
    {
        $this->task = $task;
        $this->taskTitle = $task->getTitle();
        $this->actor = $actor;
        $this->action = $action;
        $this->changes = $changes;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTask(): ?TaskItem { return $this->task; }
    public function getTaskTitle(): string { return $this->taskTitle; }
    public function getActor(): ?User { return $this->actor; }
    public function getAction(): string { return $this->action; }
    public function getChanges(): array { return $this->changes; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
