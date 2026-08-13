<?php

namespace App\Event\CRM;

use App\Entity\Contact;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class ContactCreateEvent extends Event {
    public function __construct(
        private Contact $contact,
        private User $manager
    )
    {}

    public function getContact(): Contact {
        return $this->contact;
    }

    public function getManager(): User {
        return $this->manager;
    }
}