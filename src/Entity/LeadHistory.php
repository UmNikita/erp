<?php

namespace App\Entity;

use App\CRM\Enums\TypeLeadHistory;
use App\Repository\LeadHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeadHistoryRepository::class)]
class LeadHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lead_history_records')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Lead $lead = null;

    #[ORM\ManyToOne(inversedBy: 'lead_history_records')]
    private ?User $manager = null;

    #[ORM\Column(enumType: TypeLeadHistory::class)]
    private ?TypeLeadHistory $type = null;

    #[ORM\Column(type: Types::JSON)]
    private array $data = [];

    #[ORM\Column]
    private \DateTime $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLead(): ?Lead
    {
        return $this->lead;
    }

    public function setLead(?Lead $lead): static
    {
        $this->lead = $lead;

        return $this;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    public function getType(): ?TypeLeadHistory
    {
        return $this->type;
    }

    public function setType(?TypeLeadHistory $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): static
    {
        $this->data = $data;

        return $this;
    }
    
    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
