<?php

namespace App\Entity;

use App\Repository\AccountingAllocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountingAllocationRepository::class)]
#[ORM\Table(name: 'accounting_allocation')]
#[ORM\UniqueConstraint(name: 'uniq_accounting_allocation_transaction_category', columns: ['transaction_id', 'category_id'])]
class AccountingAllocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: AccountingTransaction::class, inversedBy: 'allocations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AccountingTransaction $transaction;

    #[ORM\ManyToOne(targetEntity: AccountingCategory::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private AccountingCategory $category;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $amount;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(AccountingCategory $category, string $amount)
    {
        $this->category = $category;
        $this->amount = $amount;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTransaction(): AccountingTransaction { return $this->transaction; }
    public function setTransaction(AccountingTransaction $transaction): self { $this->transaction = $transaction; return $this; }
    public function getCategory(): AccountingCategory { return $this->category; }
    public function getAmount(): string { return $this->amount; }
    public function setAmount(string $amount): self { $this->amount = $amount; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
