<?php

namespace App\Entity;

use App\Repository\AccountingTransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountingTransactionRepository::class)]
#[ORM\Table(name: 'accounting_transaction')]
#[ORM\HasLifecycleCallbacks]
class AccountingTransaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    private string $type;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $amount;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $occurredAt;

    #[ORM\Column(length: 180)]
    private string $counterparty;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $included = true;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    /** @var Collection<int, AccountingAllocation> */
    #[ORM\OneToMany(mappedBy: 'transaction', targetEntity: AccountingAllocation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $allocations;

    public function __construct(string $type, string $amount, \DateTimeImmutable $occurredAt, string $counterparty, ?User $createdBy)
    {
        $this->type = $type;
        $this->amount = $amount;
        $this->occurredAt = $occurredAt;
        $this->counterparty = $counterparty;
        $this->createdBy = $createdBy;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->allocations = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getAmount(): string { return $this->amount; }
    public function setAmount(string $amount): self { $this->amount = $amount; return $this; }
    public function getOccurredAt(): \DateTimeImmutable { return $this->occurredAt; }
    public function setOccurredAt(\DateTimeImmutable $occurredAt): self { $this->occurredAt = $occurredAt; return $this; }
    public function getCounterparty(): string { return $this->counterparty; }
    public function setCounterparty(string $counterparty): self { $this->counterparty = $counterparty; return $this; }
    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): self { $this->comment = $comment ?: null; return $this; }
    public function isIncluded(): bool { return $this->included; }
    public function setIncluded(bool $included): self { $this->included = $included; return $this; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function setCreatedBy(?User $createdBy): self { $this->createdBy = $createdBy; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function getDeletedAt(): ?\DateTimeImmutable { return $this->deletedAt; }
    public function isDeleted(): bool { return $this->deletedAt !== null; }
    public function softDelete(): self { $this->deletedAt = new \DateTimeImmutable(); return $this; }
    public function restore(): self { $this->deletedAt = null; return $this; }
    /** @return Collection<int, AccountingAllocation> */
    public function getAllocations(): Collection { return $this->allocations; }
    public function addAllocation(AccountingAllocation $allocation): self { if (!$this->allocations->contains($allocation)) { $this->allocations->add($allocation); $allocation->setTransaction($this); } return $this; }
    public function removeAllocation(AccountingAllocation $allocation): self { $this->allocations->removeElement($allocation); return $this; }
    public function clearAllocations(): self { foreach ($this->allocations->toArray() as $allocation) { $this->removeAllocation($allocation); } return $this; }
}
