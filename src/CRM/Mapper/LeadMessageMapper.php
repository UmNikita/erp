<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\LeadMessages\MessageDTO;
use App\CRM\DTO\OpenAPI\LeadMessages\LeadMessagesListResponseDTO;
use App\CRM\DTO\ResponsibleDTO;
use App\Entity\LeadMessage;
use App\Home\Mapper\AbstractMapper;

class LeadMessageMapper extends AbstractMapper {

    public function entityListToResponse(array $messages, ?int $limit, ?int $beforeId): LeadMessagesListResponseDTO {
        $messagesDTO = $this->mapList($messages, function ($message) {
            return $this->entityToDTO($message);
        });
        return new LeadMessagesListResponseDTO($limit, $beforeId, $messagesDTO);
    }

    public function entityToDTO(LeadMessage $message): MessageDTO {
        if($message->getUser()) {
            $responsible = new ResponsibleDTO(
                $message->getUser()->getId(),
                $message->getUser()->getName(),
                $message->getUser()->getEmail()
            );
        }
        else {
            $responsible = null;
        }
        
        return new MessageDTO(
            $message->getId(),
            $message->getDateSend(),
            $message->getMessage(),
            $responsible
        );
    }
}