<?php

namespace App\CRM\Hydrators;

use App\CRM\DTO\Client\ClientCreateLeadDTO;
use App\CRM\DTO\Client\ClientDetailDTO;
use App\CRM\DTO\Client\ClientMetricsDTO;
use App\CRM\DTO\OpenAPI\Client\ClientRequestDTO;
use App\CRM\DTO\OpenAPI\Client\ClientUpdateRequestDTO;
use App\Entity\Client;
use App\Shared\Normilizers\CrmNormilizer;

class ClientHydrator {

    public function hydrateClient(Client $client, ClientRequestDTO | ClientUpdateRequestDTO $request) {
        $name = $request->name;
        if($name != null)
            $client->setName($name);

        $inn = $request->inn;
        if($inn != null)
            $client->setInn($inn);

        $field_of_activity = $request->field_of_activity;
        if($field_of_activity != null)
            $client->setFieldOfActivity($field_of_activity);

        $website = $request->website;
        if($website != null)
            $client->setWebsite($website);

        $phone = $request->phone;
        if($phone != null)
            $client->setPhone(CrmNormilizer::normalizePhone($phone));

        $email = $request->email;
        if($email != null)
            $client->setEmail(CrmNormilizer::normalizeEmail($email));

        $city = $request->city;
        if($city != null)
            $client->setCity($city);

        $channel = $request->channel;
        if($channel != null)
            $client->setChannel($channel);
    }

    public function hydrateRequestLead(Client $client, ClientCreateLeadDTO $request) {
        $name = $request->name;
        $client->setName($name);

        $phone = $request->phone;
        if($phone != null)
            $client->setPhone(CrmNormilizer::normalizePhone($phone));

        $email = $request->email;
        if($email != null)
            $client->setEmail(CrmNormilizer::normalizeEmail($email));

        $channel = $request->channel;
        if($channel != null)
            $client->setChannel($channel);
    }

    public function hydrateMetricsClient(ClientDetailDTO $client, ClientMetricsDTO $metrics) {
        $client->count_leads = $metrics->leadsCount;
        $client->ltv = $metrics->totalBudget;
        $client->amount_sum_leads = $metrics->totalBudget;
        $client->average_cheque = $metrics->averageBudget;
    }

}