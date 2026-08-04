<?php

namespace App\CRM\Services;

use App\CRM\DTO\LeadMessages\MessageDTO;
use App\CRM\DTO\OpenAPI\LeadMessages\LeadMessagesListResponseDTO;
use App\CRM\DTO\OpenAPI\LeadMessages\LeadMessagesRequestDTO;
use App\CRM\Mapper\LeadMessageMapper;
use App\Entity\LeadMessage;
use App\Repository\LeadMessageRepository;
use App\Repository\LeadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LeadMessageService {

    public function __construct(
        private EntityManagerInterface $em,
        private LeadRepository $leadRepository,
        private LeadMessageRepository $messageRepository,
        private LeadMessageMapper $messageMapper
    ) 
    {}

    public function getMessages(int $leadId, ?int $limit, ?int $beforeId): LeadMessagesListResponseDTO
    {
        $lead = $this->leadRepository->find($leadId);

        if (!$lead)
            throw new NotFoundHttpException('Lead not found!');

        if(!$limit)
            $limit = 50;
        $messages = $this->messageRepository->findMessages($lead, $limit, $beforeId);
        
        return $this->messageMapper->entityListToResponse($messages, $limit, $beforeId);
    }

    public function createMessage(LeadMessagesRequestDTO $dto): MessageDTO {
        $message = new LeadMessage();
        $this->messageMapper->mapRequestToEntity($message, $dto);
        $this->em->persist($message);
        $this->em->flush();
        return $this->messageMapper->entityToDTO($message);
    }

    public function updateMessage(int $id, string $text): MessageDTO {
        $message = $this->messageRepository->find($id);

        if(!$message)
            throw new NotFoundHttpException('Message not found!');

        $message->setMessage($text);
        $this->em->persist($message);
        $this->em->flush();
        return $this->messageMapper->entityToDTO($message);
    }

    public function deleteMessage(int $messageID) {

        $message = $this->messageRepository->find($messageID);

        if (!$message)
            throw new NotFoundHttpException('Сообщение не найдено');

        $this->em->remove($message);
        $this->em->flush();
    }
}