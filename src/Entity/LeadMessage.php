<?php

namespace App\Entity;

use App\Repository\LeadMessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeadMessageRepository::class)]
class LeadMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4096)]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTime $date_send = null;

    #[ORM\ManyToOne(inversedBy: 'lead_messages')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Lead $lead = null;

    #[ORM\ManyToOne(inversedBy: 'lead_messages')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    public function __construct()
    {
        $this->date_send = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getDateSend(): ?\DateTime
    {
        return $this->date_send;
    }

    public function setDateSend(\DateTime $date_send): static
    {
        $this->date_send = $date_send;

        return $this;
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
    
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
