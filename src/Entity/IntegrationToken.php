<?php

namespace App\Entity;

use App\Repository\IntegrationTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntegrationTokenRepository::class)]
class IntegrationToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column]
    private ?\DateTime $create_at = null;

    #[ORM\Column]
    private ?\DateTime $last_used = null;

    #[ORM\Column]
    private ?int $count_requests = null;

    public function __construct()
    {
        $this->create_at = new \DateTime();
        $this->last_used = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreateAt(): ?\DateTime
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTime $create_at): static
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getLastUsed(): ?\DateTime
    {
        return $this->last_used;
    }

    public function setLastUsed(\DateTime $last_used): static
    {
        $this->last_used = $last_used;

        return $this;
    }

    public function getCountRequests(): ?int
    {
        return $this->count_requests;
    }

    public function setCountRequests(int $count_requests): static
    {
        $this->count_requests = $count_requests;

        return $this;
    }
}
