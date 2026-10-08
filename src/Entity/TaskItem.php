<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'task_item')]
#[ORM\HasLifecycleCallbacks]
class TaskItem
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $title;

    #[ORM\Column(length: 3000, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 3000, nullable: true)]
    private ?string $result = null;

    #[ORM\Column(length: 24)]
    private string $status = 'open';

    #[ORM\Column(length: 16)]
    private string $priority = 'normal';

    #[ORM\Column(length: 7)]
    private string $color = '#3d70f5';

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dueAt = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $completedFor = null;

    #[ORM\Column(type: Types::JSON)]
    private array $completedForIds = [];

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $assignee;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'task_item_assignee')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $assignees;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $createdBy;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $requestedBy = null;

    #[ORM\ManyToOne(targetEntity: DutyShift::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?DutyShift $dutyShift = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(string $title, ?User $assignee, ?User $createdBy)
    {
        $this->title = $title;
        $this->assignees = new ArrayCollection();
        $this->assignee = $assignee;
        if ($assignee) $this->assignees->add($assignee);
        $this->createdBy = $createdBy;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $value): self { $this->description = $value; return $this; }
    public function getResult(): ?string { return $this->result; }
    public function setResult(?string $value): self { $this->result = $value; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $value): self {
        if ($this->status !== $value) {
            if (in_array($value, ['done', 'not_done'], true)) {
                $this->completedAt = new \DateTimeImmutable();
                $this->completedFor = $this->assignee;
                $this->completedForIds = $this->getAssigneeIds();
            } else {
                $this->completedAt = null;
                $this->completedFor = null;
                $this->completedForIds = [];
            }
        }
        $this->status = $value;
        return $this;
    }
    public function getPriority(): string { return $this->priority; }
    public function setPriority(string $value): self { $this->priority = $value; return $this; }
    public function getColor(): string { return $this->color; }
    public function setColor(string $value): self { $this->color = $value; return $this; }
    public function getDueAt(): ?\DateTimeImmutable { return $this->dueAt; }
    public function getCompletedAt(): ?\DateTimeImmutable { return $this->completedAt; }
    public function getCompletedFor(): ?User { return $this->completedFor; }
    public function getCompletedForIds(): array { return $this->completedForIds ?: ($this->completedFor?->getId() ? [$this->completedFor->getId()] : []); }
    public function setDueAt(?\DateTimeImmutable $value): self { $this->dueAt = $value; return $this; }
    public function getAssignee(): ?User { return $this->assignee; }
    /** @return User[] */
    public function getAssignees(): array { return $this->assignees->toArray() ?: ($this->assignee ? [$this->assignee] : []); }
    public function getAssigneeIds(): array { return array_values(array_filter(array_map(static fn (User $user) => $user->getId(), $this->getAssignees()))); }
    public function hasAssignee(User $user): bool { return in_array($user->getId(), $this->getAssigneeIds(), true); }
    public function setAssignee(?User $user): self { return $this->setAssignees($user ? [$user] : []); }
    /** @param User[] $users */
    public function setAssignees(array $users): self
    {
        $this->assignees->clear();
        foreach ($users as $user) if (!$this->assignees->contains($user)) $this->assignees->add($user);
        $this->assignee = $users[0] ?? null;
        return $this;
    }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getRequestedBy(): ?User { return $this->requestedBy; }
    public function setRequestedBy(?User $user): self { $this->requestedBy = $user; return $this; }
    public function getDutyShift(): ?DutyShift { return $this->dutyShift; }
    public function setDutyShift(?DutyShift $shift): self { $this->dutyShift = $shift; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}
