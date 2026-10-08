<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'task_duty_shift')]
class DutyShift
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $assignee;

    #[ORM\Column(length: 160)]
    private string $title;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $startsAt;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $endsAt;

    #[ORM\Column(type: Types::JSON)]
    private array $checklist = [];

    #[ORM\ManyToOne(targetEntity: DutySchedule::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?DutySchedule $schedule = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $occurrenceDate = null;

    #[ORM\Column(length: 7)]
    private string $color = '#8a63ea';

    #[ORM\Column(length: 3000, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $assignee, string $title, \DateTimeImmutable $startsAt, \DateTimeImmutable $endsAt, array $checklist)
    {
        $this->assignee = $assignee;
        $this->title = $title;
        $this->startsAt = $startsAt;
        $this->endsAt = $endsAt;
        $this->checklist = $checklist;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getAssignee(): User { return $this->assignee; }
    public function setAssignee(User $user): self { $this->assignee = $user; return $this; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getStartsAt(): \DateTimeImmutable { return $this->startsAt; }
    public function setStartsAt(\DateTimeImmutable $date): self { $this->startsAt = $date; return $this; }
    public function getEndsAt(): \DateTimeImmutable { return $this->endsAt; }
    public function setEndsAt(\DateTimeImmutable $date): self { $this->endsAt = $date; return $this; }
    public function getChecklist(): array { return $this->checklist; }
    public function setChecklist(array $items): self { $this->checklist = $items; return $this; }
    public function getSchedule(): ?DutySchedule { return $this->schedule; }
    public function setSchedule(?DutySchedule $value): self { $this->schedule = $value; return $this; }
    public function getOccurrenceDate(): ?\DateTimeImmutable { return $this->occurrenceDate; }
    public function setOccurrenceDate(?\DateTimeImmutable $value): self { $this->occurrenceDate = $value; return $this; }
    public function getColor(): string { return $this->color; }
    public function setColor(string $value): self { $this->color = $value; return $this; }
    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $value): self { $this->comment = $value; return $this; }
}
