<?php

namespace App\CRM\Services\History\DTO;

use App\Entity\Client;
use App\Entity\Contact;

class ChangeContact {

    private ?string $name = null;
    private ?string $secondname = null;
    private ?string $thirdname = null;
    private ?string $position = null;
    private ?string $phone = null;
    private ?string $email = null;
    private ?string $messenger = null;
    private ?string $note = null;
    private Contact $oldContact;
    
    public function setDTO(Contact $oldContact, Contact $newContact) {
        $this->oldContact = $oldContact;
        if($oldContact->getName() != $newContact->getName())
            $this->name = $newContact->getName();

        if($oldContact->getSecondname() != $newContact->getSecondname())
            $this->secondname = $newContact->getSecondname();

        if($oldContact->getThirdname() != $newContact->getThirdname())
            $this->thirdname = $newContact->getThirdname();

        if($oldContact->getPosition() != $newContact->getPosition())
            $this->position = $newContact->getPosition();

        if($oldContact->getPhone() != $newContact->getPhone())
            $this->phone = $newContact->getPhone();

        if($oldContact->getEmail() != $newContact->getEmail())
            $this->email = $newContact->getEmail();

        if($oldContact->getMessenger() != $newContact->getMessenger())
            $this->messenger = $newContact->getMessenger();

        if($oldContact->getNote() != $newContact->getNote())
            $this->note = $newContact->getNote();
    }

    public function toArray(): array
    {
        $obj = [];
        if($this->name) {
            $obj['name'] = [
                'to' => $this->name,
                'from' => $this->oldContact->getName()
            ];
        }
        if($this->secondname) {
            $obj['secondname'] = [
                'to' => $this->secondname,
                'from' => $this->oldContact->getSecondname()
            ];
        }
        if($this->thirdname) {
            $obj['thirdname'] = [
                'to' => $this->thirdname,
                'from' => $this->oldContact->getThirdname()
            ];
        }
        if($this->position) {
            $obj['position'] = [
                'to' => $this->position,
                'from' => $this->oldContact->getPosition()
            ];
        }
        if($this->phone) {
            $obj['phone'] = [
                'to' => $this->phone,
                'from' => $this->oldContact->getPhone()
            ];
        }
        if($this->email) {
            $obj['email'] = [
                'to' => $this->email,
                'from' => $this->oldContact->getEmail()
            ];
        }
        if($this->messenger) {
            $obj['messenger'] = [
                'to' => $this->messenger,
                'from' => $this->oldContact->getMessenger()
            ];
        }
        if($this->note) {
            $obj['note'] = [
                'to' => $this->note,
                'from' => $this->oldContact->getNote()
            ];
        }

        //dd($obj);
            
        return $obj;
    }

}