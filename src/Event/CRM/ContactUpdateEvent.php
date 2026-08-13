<?php

namespace App\Event\CRM;

use App\Entity\Contact;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class ContactUpdateEvent extends Event {
    public function __construct(
        private Contact $oldContact,
        private Contact $contact,
        private User $manager
    )
    {}

    public function getContact(): Contact {
        return $this->contact;
    }
    
    public function getOldContact(): Contact {
        return $this->oldContact;
    }

    public function getManager(): User {
        return $this->manager;
    }
}