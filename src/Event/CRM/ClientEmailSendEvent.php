<?php

namespace App\Event\CRM;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class ClientEmailSendEvent extends Event {
    public function __construct(
        private string $to,
        private User $manager,
        private bool $isKp,
        private ?string $title = null
    )
    {}

    public function getTo(): string {
        return $this->to;
    }

    public function getManager(): User {
        return $this->manager;
    }

    public function getIsKp(): string {
        return $this->isKp;
    }
    
    public function getTitle(): string {
        return $this->title;
    }
}