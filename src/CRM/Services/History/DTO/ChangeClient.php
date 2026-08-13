<?php

namespace App\CRM\Services\History\DTO;

use App\Entity\Client;

class ChangeClient {

    private ?string $name = null;
    private ?string $inn = null;
    private ?string $field_of_activity = null;
    private ?string $website = null;
    private ?string $phone = null;
    private ?string $email = null;
    private ?string $city = null;
    private ?string $channel = null;
    private Client $oldClient;
    
    public function setDTO(Client $oldClient, Client $newClient) {
        $this->oldClient = $oldClient;
        if($oldClient->getName() != $newClient->getName())
            $this->name = $newClient->getName();

        if($oldClient->getInn() != $newClient->getInn())
            $this->inn = $newClient->getInn();

        if($oldClient->getFieldOfActivity() != $newClient->getFieldOfActivity())
            $this->field_of_activity = $newClient->getFieldOfActivity();

        if($oldClient->getWebsite() != $newClient->getWebsite())
            $this->website = $newClient->getWebsite();

        if($oldClient->getPhone() != $newClient->getPhone())
            $this->phone = $newClient->getPhone();

        if($oldClient->getEmail() != $newClient->getEmail())
            $this->email = $newClient->getEmail();

        if($oldClient->getCity() != $newClient->getCity())
            $this->city = $newClient->getCity();

        if($oldClient->getChannel() != $newClient->getChannel())
            $this->channel = $newClient->getChannel();
    }

    public function toArray(): array
    {
        $obj = [];
        if($this->name) {
            $obj['name'] = [
                'to' => $this->name,
                'from' => $this->oldClient->getName()
            ];
        }
        if($this->inn) {
            $obj['inn'] = [
                'to' => $this->inn,
                'from' => $this->oldClient->getInn()
            ];
        }
        if($this->field_of_activity) {
            $obj['field_of_activity'] = [
                'to' => $this->field_of_activity,
                'from' => $this->oldClient->getFieldOfActivity()
            ];
        }
        if($this->website) {
            $obj['website'] = [
                'to' => $this->website,
                'from' => $this->oldClient->getWebsite()
            ];
        }
        if($this->phone) {
            $obj['phone'] = [
                'to' => $this->phone,
                'from' => $this->oldClient->getPhone()
            ];
        }
        if($this->email) {
            $obj['email'] = [
                'to' => $this->email,
                'from' => $this->oldClient->getEmail()
            ];
        }
        if($this->city) {
            $obj['city'] = [
                'to' => $this->city,
                'from' => $this->oldClient->getCity()
            ];
        }
        if($this->channel) {
            $obj['channel'] = [
                'to' => $this->channel,
                'from' => $this->oldClient->getChannel()
            ];
        }
            
        return $obj;
    }

}