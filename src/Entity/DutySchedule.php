<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'task_duty_schedule')]
class DutySchedule
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    private string $title;

    #[ORM\Column(length: 7)]
    private string $color = '#8a63ea';

    #[ORM\Column(length: 3000, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active = true;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $sameTime = true;

    #[ORM\Column(type: Types::JSON)]
    private array $days = [];

    #[ORM\Column(type: Types::JSON)]
    private array $taskTemplates = [];

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $effectiveFrom;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastGeneratedDate = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $title)
    {
        $this->title = $title;
        $this->effectiveFrom = new \DateTimeImmutable('today', new \DateTimeZone('Europe/Moscow'));
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $value): self { $this->title = $value; return $this; }
    public function getColor(): string { return $this->color; }
    public function setColor(string $value): self { $this->color = $value; return $this; }
    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $value): self { $this->comment = $value; return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $value): self { $this->active = $value; return $this; }
    public function isSameTime(): bool { return $this->sameTime; }
    public function setSameTime(bool $value): self { $this->sameTime = $value; return $this; }
    public function getDays(): array { return $this->days; }
    public function setDays(array $value): self { $this->days = $value; return $this; }
    public function getTaskTemplates(): array { return $this->taskTemplates; }
    public function setTaskTemplates(array $value): self { $this->taskTemplates = $value; return $this; }
    public function getEffectiveFrom(): \DateTimeImmutable { return $this->effectiveFrom; }
    public function setEffectiveFrom(\DateTimeImmutable $value): self { $this->effectiveFrom = $value; return $this; }
    public function getLastGeneratedDate(): ?\DateTimeImmutable { return $this->lastGeneratedDate; }
    public function setLastGeneratedDate(?\DateTimeImmutable $value): self { $this->lastGeneratedDate = $value; return $this; }
}
