<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, options: ['default' => 'Пользователь'])]
    private ?string $name = 'Admin';

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $is_active = true;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $is_root = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $created_at = null;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Role $role = null;

    /**
     * @var Collection<int, LeadMessage>
     */
    #[ORM\OneToMany(mappedBy: 'lead', targetEntity: LeadMessage::class)]
    private Collection $lead_messages;

    /**
     * @var Collection<int, Lead>
     */
    #[ORM\OneToMany(mappedBy: 'responsible', targetEntity: Lead::class)]
    private Collection $leads;

    /**
     * @var Collection<int, LeadHistory>
     */
    #[ORM\OneToMany(targetEntity: LeadHistory::class, mappedBy: 'manager')]
    private Collection $lead_history_records;

    /**
     * @var Collection<int, ClientHistory>
     */
    #[ORM\OneToMany(targetEntity: ClientHistory::class, mappedBy: 'manager')]
    private Collection $client_history_records;

    /**
     * @var Collection<int, EmailLog>
     */
    #[ORM\OneToMany(targetEntity: EmailLog::class, mappedBy: 'ManyToOne')]
    private Collection $email_logs;

    public function __construct()
    {
        $this->lead_messages = new ArrayCollection();
        $this->leads = new ArrayCollection();
        $this->lead_history_records = new ArrayCollection();
        $this->client_history_records = new ArrayCollection();
        $this->email_logs = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->created_at = new \DateTime();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }


    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;
        return $this;
    }

    public function isRoot(): bool
    {
        return $this->is_root;
    }

    public function setIsRoot(bool $is_root): self
    {
        $this->is_root = $is_root;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        if ($this->is_root) {
            $roles[] = 'ROLE_ADMIN';
        }
        return array_unique($roles);
    }

    public function getLeadMessages(): Collection
    {
        return $this->lead_messages;
    }

    public function addLeadMessage(LeadMessage $leadMessage): static
    {
        if (!$this->lead_messages->contains($leadMessage)) {
            $this->lead_messages->add($leadMessage);
            $leadMessage->setUser($this);
        }

        return $this;
    }

    public function removeLeadMessage(LeadMessage $leadMessage): static
    {
        if ($this->lead_messages->removeElement($leadMessage)) {
            if ($leadMessage->getUser() === $this) {
                $leadMessage->setUser(null);
            }
        }

        return $this;
    }

    public function getLeads(): Collection
    {
        return $this->leads;
    }

    public function addLead(Lead $lead): static
    {
        if (!$this->leads->contains($lead)) {
            $this->leads->add($lead);
            $lead->setResponsible($this);
        }

        return $this;
    }

    public function removeLead(Lead $lead): static
    {
        if ($this->leads->removeElement($lead)) {
            if ($lead->getResponsible() === $this) {
                $lead->setResponsible(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function hasPermission(string $permissionName): bool
    {
        if($this->getRole() == null) 
            return false;
        
        foreach ($this->getRole()->getPermissions() as $permission) {
            if ($permission->getName() === $permissionName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, LeadHistory>
     */
    public function getLeadHistoryRecords(): Collection
    {
        return $this->lead_history_records;
    }

    public function addLeadHistoryRecord(LeadHistory $leadHistoryRecord): static
    {
        if (!$this->lead_history_records->contains($leadHistoryRecord)) {
            $this->lead_history_records->add($leadHistoryRecord);
            $leadHistoryRecord->setManager($this);
        }

        return $this;
    }

    public function removeLeadHistoryRecord(LeadHistory $leadHistoryRecord): static
    {
        if ($this->lead_history_records->removeElement($leadHistoryRecord)) {
            // set the owning side to null (unless already changed)
            if ($leadHistoryRecord->getManager() === $this) {
                $leadHistoryRecord->setManager(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ClientHistory>
     */
    public function getClientHistoryRecords(): Collection
    {
        return $this->client_history_records;
    }

    public function addClientHistoryRecord(ClientHistory $clientHistoryRecord): static
    {
        if (!$this->client_history_records->contains($clientHistoryRecord)) {
            $this->client_history_records->add($clientHistoryRecord);
            $clientHistoryRecord->setManager($this);
        }

        return $this;
    }

    public function removeClientHistoryRecord(ClientHistory $clientHistoryRecord): static
    {
        if ($this->client_history_records->removeElement($clientHistoryRecord)) {
            // set the owning side to null (unless already changed)
            if ($clientHistoryRecord->getManager() === $this) {
                $clientHistoryRecord->setManager(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EmailLog>
     */
    public function getEmailLogs(): Collection
    {
        return $this->email_logs;
    }

    public function addEmailLog(EmailLog $emailLog): static
    {
        if (!$this->email_logs->contains($emailLog)) {
            $this->email_logs->add($emailLog);
            $emailLog->setUser($this);
        }

        return $this;
    }

    public function removeEmailLog(EmailLog $emailLog): static
    {
        if ($this->email_logs->removeElement($emailLog)) {
            // set the owning side to null (unless already changed)
            if ($emailLog->getUser() === $this) {
                $emailLog->setUser(null);
            }
        }

        return $this;
    }

}
