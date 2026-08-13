<?php

namespace App\CRM\Services;

use App\CRM\DTO\PaginationDTO;
use App\CRM\DTO\Lead\LeadDetailDTO;
use App\CRM\DTO\Lead\LeadDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadListResponseDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadRequestDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadUpdateRequestDTO;
use App\CRM\Enums\LeadStatus;
use App\CRM\Mapper\ClientMapper;
use App\CRM\Mapper\LeadMapper;
use App\Entity\Client;
use App\Entity\Lead;
use App\Event\CRM\LeadCreatedEvent;
use App\Event\CRM\LeadUpdateEvent;
use App\Repository\ClientRepository;
use App\Repository\LeadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LeadService {

    public function __construct(
        private ClientRepository $clientRepository,
        private LeadMapper $leadMapper,
        private ClientMapper $clientMapper,
        private LeadRepository $leadRepository,
        private EntityManagerInterface $em,
        private Security $security,
        private EventDispatcherInterface $eventDispatcher
    ) 
    {}

    public function getDetailLead(int $id): LeadDetailDTO {
        $lead = $this->leadRepository->findWithClientAndContacts($id);

        if (!$lead)
            throw new NotFoundHttpException('Lead not found');
        
        $leadDTO = $this->leadMapper->entityToDetailResponse($lead);

        if($leadDTO->client != null) {
            $clientID = $leadDTO->client->id;
            $metrics = $this->clientRepository->getMetricsClient($clientID);
            $this->clientMapper->mapMetricsToClientDetailDTO($leadDTO->client, $metrics);
        }
        
        return $leadDTO;
    }

    public function updateLead(int $id, LeadUpdateRequestDTO $request): LeadDTO | array {
        return $this->em->wrapInTransaction(function () use ($request, $id) {
            if($request->isEmpty())
                return ["status" => "Empty body"];

            $lead = $this->leadRepository->find($id);

            if (!$lead)
                throw new NotFoundHttpException('Stage not found!');

            $oldLead = clone $lead;

            $this->leadMapper->mapRequestToEntity($lead, $request);

            $this->em->persist($lead);
            $this->em->flush();

            $manager = $this->security->getUser();
            $event = new LeadUpdateEvent($oldLead, $lead, $manager);
            $this->eventDispatcher->dispatch($event);

            return $this->leadMapper->entityToDTO($lead);
        });
    }

    public function createLead(LeadRequestDTO $request): LeadDTO {
        return $this->em->wrapInTransaction(function () use ($request) {
            $lead = new Lead();
            $this->leadMapper->mapRequestToEntity($lead, $request);
            $lead->setStatus(LeadStatus::ACTIVE);
            if($lead->getClient() == null && $request->client) {
                $client = new Client();
                $this->clientMapper->mapRequestLeadToEntity($client, $request->client);
                $lead->setClient($client);
                $this->em->persist($client);
            }
            $this->em->persist($lead);
            $this->em->flush();

            $manager = $this->security->getUser();
            $event = new LeadCreatedEvent($lead, $manager);
            $this->eventDispatcher->dispatch($event);

            return $this->leadMapper->entityToDTO($lead);
        });
    }

    public function deleteLead(int $id)
    {

        $lead = $this->leadRepository->find($id);
        if (!$lead)
            throw new NotFoundHttpException('Lead not found!');
                
        $this->em->remove($lead);
        $this->em->flush();
    }
}