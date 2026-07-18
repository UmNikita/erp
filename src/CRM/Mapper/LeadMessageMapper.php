<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\LeadMessages\MessageDTO;
use App\CRM\DTO\OpenAPI\LeadMessages\LeadMessagesListResponseDTO;
use App\CRM\DTO\OpenAPI\LeadMessages\LeadMessagesRequestDTO;
use App\Entity\LeadMessage;
use App\Home\Mapper\AbstractMapper;
use App\Repository\LeadRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LeadMessageMapper extends AbstractMapper {

    public function __construct(
        private UserRepository $userRepository,
        private LeadRepository $leadRepository
    )
    {}

    public function entityListToResponse(array $messages): LeadMessagesListResponseDTO {
        $messagesDTO = $this->mapList($messages, function ($message) {
            return $this->entityToDTO($message);
        });
        return new LeadMessagesListResponseDTO(null, null, $messagesDTO);
    }

    public function entityToDTO(LeadMessage $message): MessageDTO {
        return new MessageDTO(
            $message->getId(),
            $message->getUser()->getName(),
            $message->getUser()->getId(),
            $message->getDateSend(),
            $message->getMessage()
        );
    }

    public function mapRequestToEntity(LeadMessage $message, LeadMessagesRequestDTO $request) {
        
        $message->setMessage($request->message);

        $user = $this->userRepository->find($request->user_id);
        if (!$user)
            throw new NotFoundHttpException('Пользователь не найден');
        $message->setUser($user);

        $lead = $this->leadRepository->find($request->lead_id);
        if (!$lead)
            throw new NotFoundHttpException('Сделка не найдена');
        $message->setLead($lead);
    }
}