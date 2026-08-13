<?php

namespace App\Entity;

use App\CRM\Enums\TypeClientHistory;
use App\Repository\ClientHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientHistoryRepository::class)]
class ClientHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'client_history_records')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'client_history_records')]
    private ?User $manager = null;

    #[ORM\Column(enumType: TypeClientHistory::class)]
    private ?TypeClientHistory $type = null;

    #[ORM\Column]
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

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

    public function getType(): ?TypeClientHistory
    {
        return $this->type;
    }

    public function setType(?TypeClientHistory $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
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
