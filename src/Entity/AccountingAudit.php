<?php

namespace App\Entity;

use App\Repository\AccountingAuditRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountingAuditRepository::class)]
#[ORM\Table(name: 'accounting_audit')]
class AccountingAudit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user;

    #[ORM\Column(length: 80)]
    private string $action;

    #[ORM\Column(length: 40)]
    private string $entityType;

    #[ORM\Column(nullable: true)]
    private ?int $entityId;

    #[ORM\Column(type: Types::JSON)]
    private array $payload;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(?User $user, string $action, string $entityType, ?int $entityId, array $payload = [])
    {
        $this->user = $user;
        $this->action = $action;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->payload = $payload;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function getAction(): string { return $this->action; }
    public function getEntityType(): string { return $this->entityType; }
    public function getEntityId(): ?int { return $this->entityId; }
    public function getPayload(): array { return $this->payload; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
