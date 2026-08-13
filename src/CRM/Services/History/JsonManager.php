<?php

namespace App\CRM\Services\History;

use App\CRM\Enums\TypeClientHistory;
use App\CRM\Enums\TypeLeadHistory;
use App\Entity\Client;
use App\Entity\ClientHistory;
use App\Entity\Contact;
use App\Entity\Lead;
use App\Entity\LeadHistory;
use App\Entity\User;

class JsonManager {

    public function __construct(
        private ParserLeadJSON $leadParser,
        private ParserClientJSON $clientParser
    )
    {}

    public function getLeadHistory(Lead $lead, User $user, TypeLeadHistory $type, ?Lead $oldLead = null): LeadHistory {
        $record = new LeadHistory();
        $record->setLead($lead);
        $record->setManager($user);
        $json = $this->leadParser->parseToJSON($lead, $type, $oldLead);
        $record->setData($json);
        $record->setType($type);
        return $record;
    }

    public function getClientHistory(Client $client, User $user, TypeClientHistory $type, ?Client $oldClient= null, ?Lead $lead = null, ?Contact $contact = null, ?Contact $oldContact = null): ClientHistory {
        $record = new ClientHistory();
        $record->setClient($client);
        $record->setManager($user);
        $json = $this->clientParser->parseToJSON($client, $type, $oldClient, $lead, $contact, $oldContact);
        $record->setData($json);
        $record->setType($type);
        return $record;
    }
    
    public function getMessagesLead(array $leads): array {
        return $this->leadParser->parseToText($leads);
    }

    public function getMessagesClient(array $clients): array {
        return $this->clientParser->parseToText($clients);
    }

}