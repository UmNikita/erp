<?php

namespace App\CRM\Services\History;

use App\CRM\DTO\HistoryDTO;
use App\CRM\Enums\LeadStatus;
use App\CRM\Enums\TypeClientHistory;
use App\CRM\Services\History\DTO\ChangeClient;
use App\CRM\Services\History\DTO\ChangeContact;
use App\Entity\Client;
use App\Entity\ClientHistory;
use App\Entity\Contact;
use App\Entity\Lead;
use App\Repository\ContactRepository;
use App\Repository\LeadRepository;
use RuntimeException;

class ParserClientJSON {

    public function __construct(
        private LeadRepository $leadRepository,
        private ContactRepository $contactRepository
    )
    {}
    
    public function parseToJSON(Client $client, TypeClientHistory $type, ?Client $oldClient = null, ?Lead $lead = null, ?Contact $contact = null, ?Contact $oldContact = null): array {
        switch ($type) {
            case TypeClientHistory::UPDATED: {
                return $this->parseUpdated($client, $oldClient);
            }
            case TypeClientHistory::LEAD_CREATED: {
                return $this->parseLeadCreate($lead);
            }
            case TypeClientHistory::LEAD_APPOINTED: {
                return $this->parseLeadCreate($lead);
            }
            case TypeClientHistory::LEAD_FINISH: {
                return $this->parseLeadFinish($lead);
            }
            // case TypeClientHistory::EMAIL_SENDED: {
            //     return $this->parseStageChanged($lead, $oldLead);
            // }
            case TypeClientHistory::KP_SENDED: {
                return [];
            }
            case TypeClientHistory::CONTACT_CREATED: {
                return $this->parseContactCreate($contact);
            }
            case TypeClientHistory::CONTACT_UPDATED: {
                return $this->parseContactUpdated($contact, $oldContact);
            }
            case TypeClientHistory::CONTACT_DELETE: {
                return $this->parseContactDelete($contact);
            }
        }
        return [];
    }

    private function parseUpdated(Client $client, ?Client $oldClient): array {
        if($oldClient == null)
            throw new RuntimeException("Parsing error! Don't have old client!");

        $dto = new ChangeClient();
        $dto->setDTO($oldClient, $client);
        return $dto->toArray();
    }

    private function parseLeadCreate(Lead $lead): array {
        if($lead == null)
            throw new RuntimeException("Parsing error! Don't have lead!");

        return ['lead_id' => $lead->getId()];
    }

    private function parseLeadFinish(Lead $lead): array {
        if($lead == null)
            throw new RuntimeException("Parsing error! Don't have lead!");

        return ['lead_id' => $lead->getId(), 'status' => $lead->getStatus()];
    }

    private function parseContactCreate(Contact $contact): array {
        if($contact == null)
            throw new RuntimeException("Parsing error! Don't have contact!");

        return ['contact_id' => $contact->getId()];
    }

    private function parseContactUpdated(Contact $contact, ?Contact $oldContact): array {
        if($oldContact == null)
            throw new RuntimeException("Parsing error! Don't have old contact!");

        $dto = new ChangeContact();
        $dto->setDTO($oldContact, $contact);
        return $dto->toArray();
    }

    private function parseContactDelete(Contact $contact): array {
        if($contact == null)
            throw new RuntimeException("Parsing error! Don't have contact!");

        return ['contact_id' => $contact->getId()];
    }

    /** @var ClientHistory[] $records */
    public function parseToText(array $records): array {
        $leadIds = $this->getLeadsHashTable($records);
        $contactIds = $this->getContactsHashTable($records);
        $result = [];

        foreach ($records as $record) {
            $message = "";
            switch ($record->getType()) {
                case TypeClientHistory::UPDATED: {
                    $data = $record->getData();
                    $message = $this->getMessagesUpdate($data);
                    break;
                }
                case TypeClientHistory::LEAD_CREATED: {
                    $data = $record->getData();
                    if(!$data["lead_id"])
                        throw new \RuntimeException("Invalid data!");
                    
                    $lead = $leadIds[$data["lead_id"]];
                    $message = "Сделка  «". $lead->getName() ."» создана в этапе «". $lead->getStage()->getName() ."» в воронке «". $lead->getStage()->getPipeline()->getName() ."»!";
                    break;
                }
                case TypeClientHistory::LEAD_APPOINTED: {
                    $data = $record->getData();
                    if(!$data["lead_id"])
                        throw new \RuntimeException("Invalid data!");

                    $lead = $leadIds[$data["lead_id"]];
                    $message = "Сделка  «". $lead->getName() ."» создана в этапе «". $lead->getStage()->getName() ."» в воронке «". $lead->getStage()->getPipeline()->getName() ."»!";
                    break;
                }
                case TypeClientHistory::LEAD_FINISH: {
                    $data = $record->getData();
                    if(!$data["lead_id"] && !$data["status"])
                        throw new \RuntimeException("Invalid data!");
                    
                    if(!isset($leadIds[$data['lead_id']]))
                        $lead = "Сделка (".$data["lead_id"].") создана!";
                    else {
                        $obj = $leadIds[$data['lead_id']];
                        $lead = $obj->getName();
                    }

                    if($data["status"] == LeadStatus::LOST->value) {
                        $message = "Сделка «". $lead ."» завершена неудачно!";
                    }
                    else {
                        $message = "Сделка «". $lead ."» завершена успешно!";
                    }

                    break;
                }
                case TypeClientHistory::EMAIL_SENDED: {
                    $message = "";
                    break;
                }
                case TypeClientHistory::KP_SENDED: {
                    $message = "Отправлено КП!";
                    break;
                }
                case TypeClientHistory::CONTACT_CREATED: {
                    $data = $record->getData();
                    if(!$data["contact_id"])
                        throw new \RuntimeException("Invalid data!");
                    if(!isset($contactIds[$data['contact_id']]))
                        $message = "Контакт (".$data["contact_id"].") создан!";
                    else {
                        $contact = $contactIds[$data["contact_id"]];
                        $message = "Контакт «". $contact->getName() ."» создан!";
                    }
                    break;
                }
                case TypeClientHistory::CONTACT_UPDATED: {
                    $data = $record->getData();
                    $message = $this->getMessagesContactUpdate($data);
                    break;
                }
                case TypeClientHistory::CONTACT_DELETE: {
                    $data = $record->getData();
                    if(!$data["contact_id"])
                        throw new \RuntimeException("Invalid data!");
                    
                    $message = "Контакт (".$data["contact_id"].") удален!";

                    break;
                }
            }
            if($message == "")
                continue;
            $result[] = new HistoryDTO($message, $record->getManager()->getName(), $record->getCreatedAt());
        }

        return $result;
    }

    private function getMessagesContactUpdate(array $data): string {
        $message = "";
        foreach ($data as $field => $value) {
            if($field == 'name') {
                $message .= $this->buildMiniNessage($data['name'], 'Имя', 's');
                continue;
            }
            if($field == 'secondname') {
                $message .= $this->buildMiniNessage($data['secondname'], 'Фамилия', 'f');
                continue;
            }
            if($field == 'thirdname') {
                $message .= $this->buildMiniNessage($data['thirdname'], 'Отчество', 's');
                continue;
            }
            if($field == 'position') {
                $message .= $this->buildMiniNessage($data['position'], 'Должность', 'f');
                continue;
            }
            if($field == 'phone') {
                $message .= $this->buildMiniNessage($data['phone'], 'Телефон', 'm');
                continue;
            }
            if($field == 'email') {
                $message .= $this->buildMiniNessage($data['email'], 'Почта', 'f');
                continue;
            }
            if($field == 'messenger') {
                $message .= $this->buildMiniNessage($data['messenger'], 'Мессенджер', 'm');
                continue;
            }
            if($field == 'note') {
                $message .= $this->buildMiniNessage($data['note'], 'Заметка', 'f');
                continue;
            }
        }
        return $message;
    }

    private function getMessagesUpdate(array $data): string {
        $message = "";

        foreach ($data as $field => $value) {
            if($field == 'name') {
                $message .= $this->buildMiniNessage($data['name'], 'Имя', 's');
                continue;
            }
            if($field == 'inn') {
                $message .= $this->buildMiniNessage($data['inn'], 'ИНН', 'm');
                continue;
            }
            if($field == 'field_of_activity') {
                $message .= $this->buildMiniNessage($data['field_of_activity'], 'Сфера деятельности', 'f');
                continue;
            }
            if($field == 'website') {
                $message .= $this->buildMiniNessage($data['website'], 'Вебсайт', 'm');
                continue;
            }
            if($field == 'phone') {
                $message .= $this->buildMiniNessage($data['phone'], 'Телефон', 'm');
                continue;
            }
            if($field == 'email') {
                $message .= $this->buildMiniNessage($data['email'], 'Почта', 'f');
                continue;
            }
            if($field == 'city') {
                $message .= $this->buildMiniNessage($data['city'], 'Город', 'm');
                continue;
            }
            if($field == 'channel') {
                $message .= $this->buildMiniNessage($data['channel'], 'Канал', 'm');
                continue;
            }
        }
        return $message;
    }

    private function buildMiniNessage(array $data, string $field, string $gen): string {
        if(!$data["from"] && !$data["to"])
            throw new \RuntimeException("Invalid data!");

        if($gen == 'm')
            $word = "изменен";
        else if ($gen == 'f')
            $word = "изменена";
        else 
            $word = "изменено";

        return "". $field ." ". $word ." с «". $data["from"] ."» на «". $data["to"] ."»; ";
    }

    /** @var ClientHistory[] $records */
    private function getLeadsHashTable(array $records): array {
        $leadIds = [];
        foreach ($records as $record) {
            if($record->getType() == TypeClientHistory::LEAD_CREATED || $record->getType() == TypeClientHistory::LEAD_APPOINTED) {
                $data = $record->getData();
                if(!$data["lead_id"])
                    throw new \RuntimeException("Invalid data!");

                $leadIds[$data["lead_id"]] = null;
            }
        }
        $leads = $this->leadRepository->findLeadsWithStagesHashTable(array_keys($leadIds));
        $leads = array_combine(
            array_map(fn (Lead $stage) => $stage->getId(), $leads),
            $leads
        );
        return $leads;
    }

     /** @var ClientHistory[] $records */
    private function getContactsHashTable(array $records): array {
        $contactIds = [];
        foreach ($records as $record) {
            if($record->getType() == TypeClientHistory::CONTACT_CREATED) {
                $data = $record->getData();
                if(!$data["contact_id"])
                    throw new \RuntimeException("Invalid data!");

                $contactIds[$data["contact_id"]] = null;
            }
        }
        $contacts = $this->contactRepository->findContactsHashTable(array_keys($contactIds));
        $contacts = array_combine(
            array_map(fn (Contact $contact) => $contact->getId(), $contacts),
            $contacts
        );
        return $contacts;
    }
}