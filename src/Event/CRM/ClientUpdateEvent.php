<?php

namespace App\Event\CRM;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class ClientUpdateEvent extends Event {
    public function __construct(
        private Client $client,
        private Client $oldClient,
        private User $manager
    )
    {}

    public function getClient(): Client {
        return $this->client;
    }
    
    public function getOldClient(): Client {
        return $this->oldClient;
    }

    public function getManager(): User {
        return $this->manager;
    }
}