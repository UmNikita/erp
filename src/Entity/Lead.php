<?php

namespace App\Entity;

use App\CRM\Enums\LeadStatus;
use App\Repository\LeadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeadRepository::class)]
class Lead
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(options: ['default' => 0])]
    private ?float $budget = 0;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $product = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $source  = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $next_action  = null;

    #[ORM\Column]
    private ?\DateTime $date_start = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date_next_action = null;

    #[ORM\ManyToOne(inversedBy: 'leads')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $responsible = null;

    #[ORM\ManyToOne(inversedBy: 'leads')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Client $client = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(enumType: LeadStatus::class)]
    private LeadStatus $status = LeadStatus::ACTIVE;

    #[ORM\ManyToOne(inversedBy: 'stages')]
    private ?Stage $stage = null;

     /**
     * @var Collection<int, LeadMessage>
     */
    #[ORM\OneToMany(mappedBy: 'lead', targetEntity: LeadMessage::class)]
    private Collection $lead_messages;

    public function __construct()
    {
        $this->date_start = new \DateTime();
        $this->lead_messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): static
    {
        $this->budget = $budget;

        return $this;
    }
    
    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): static
    {
        $this->product = $product;

        return $this;
    }
    
    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }
    
    public function getNextAction(): ?string
    {
        return $this->next_action;
    }

    public function setNextAction(string $next_action): static
    {
        $this->next_action = $next_action;

        return $this;
    }

    public function getDateStart(): ?\DateTime
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTime $date_start): static
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateNextAction(): ?\DateTime
    {
        return $this->date_next_action;
    }

    public function setDateNextAction(?\DateTime $date_next_action): static
    {
        $this->date_next_action = $date_next_action;

        return $this;
    }

    public function getResponsible(): ?User
    {
        return $this->responsible;
    }

    public function setResponsible(?User $responsible): static
    {
        $this->responsible = $responsible;

        return $this;
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getStatus(): LeadStatus
    {
        return $this->status;
    }

    public function setStatus(LeadStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStage(): ?Stage
    {
        return $this->stage;
    }

    public function setStage(?Stage $stage): static
    {
        $this->stage = $stage;

        return $this;
    }

    public function getLeadMessages(): Collection
    {
        return $this->lead_messages;
    }

    public function addLeadMessage(LeadMessage $leadMessage): static
    {
        if (!$this->lead_messages->contains($leadMessage)) {
            $this->lead_messages->add($leadMessage);
            $leadMessage->setLead($this);
        }

        return $this;
    }

    public function removeLeadMessage(LeadMessage $leadMessage): static
    {
        if ($this->lead_messages->removeElement($leadMessage)) {
            if ($leadMessage->getLead() === $this) {
                $leadMessage->setLead(null);
            }
        }

        return $this;
    }
}
