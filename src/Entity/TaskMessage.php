<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'task_message')]
class TaskMessage
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TaskItem::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private TaskItem $task;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $author;

    #[ORM\Column(length: 3000)]
    private string $body;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct(TaskItem $task, ?User $author, string $body)
    {
        $this->task = $task;
        $this->author = $author;
        $this->body = $body;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTask(): TaskItem { return $this->task; }
    public function getAuthor(): ?User { return $this->author; }
    public function getBody(): string { return $this->body; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
