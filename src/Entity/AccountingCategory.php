<?php

namespace App\Entity;

use App\Repository\AccountingCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountingCategoryRepository::class)]
#[ORM\Table(name: 'accounting_category')]
#[ORM\UniqueConstraint(name: 'uniq_accounting_category_name_type', columns: ['name', 'type'])]
class AccountingCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private string $name;

    #[ORM\Column(length: 16)]
    private string $type;

    #[ORM\Column(length: 16)]
    private string $icon = '✨';

    #[ORM\Column(length: 7)]
    private string $color = '#3f7af6';

    #[ORM\Column(options: ['default' => true])]
    private bool $active = true;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $name, string $type, ?User $createdBy = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->createdBy = $createdBy;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getIcon(): string { return $this->icon; }
    public function setIcon(string $icon): self { $this->icon = $icon; return $this; }
    public function getColor(): string { return $this->color; }
    public function setColor(string $color): self { $this->color = $color; return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): self { $this->active = $active; return $this; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
