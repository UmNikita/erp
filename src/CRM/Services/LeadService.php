<?php

namespace App\CRM\Services;

use App\CRM\DTO\Lead\LeadDetailDTO;
use App\CRM\DTO\Lead\LeadDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadRequestDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadUpdateRequestDTO;
use App\CRM\Enums\LeadStatus;
use App\CRM\Mapper\ClientMapper;
use App\CRM\Mapper\LeadMapper;
use App\Entity\Lead;
use App\Repository\ClientRepository;
use App\Repository\LeadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LeadService {

    public function __construct(
        private ClientRepository $clientRepository,
        private LeadMapper $leadMapper,
        private ClientMapper $clientMapper,
        private LeadRepository $leadRepository,
        private EntityManagerInterface $em,
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
        if($request->isEmpty())
            return ["status" => "Empty body"];

        $lead = $this->leadRepository->find($id);

        if (!$lead)
            throw new NotFoundHttpException('Stage not found!');

        $this->leadMapper->mapRequestToEntity($lead, $request);

        $this->em->persist($lead);
        $this->em->flush();

        return $this->leadMapper->entityToDTO($lead);
    }

    public function createLead(LeadRequestDTO $request): LeadDTO {
        $lead = new Lead();

        $this->leadMapper->mapRequestToEntity($lead, $request);
        $lead->setStatus(LeadStatus::ACTIVE);

        $this->em->persist($lead);
        $this->em->flush();

        return $this->leadMapper->entityToDTO($lead);
    }
}