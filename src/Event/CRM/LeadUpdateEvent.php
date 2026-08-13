<?php

namespace App\Event\CRM;

use App\Entity\Lead;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class LeadUpdateEvent extends Event {
    public function __construct(
        private Lead $oldLead,
        private Lead $lead,
        private User $manager
    )
    {}

    public function getOldLead(): Lead {
        return $this->oldLead;
    }

    public function getLead(): Lead {
        return $this->lead;
    }

    public function getManager(): User {
        return $this->manager;
    }
}