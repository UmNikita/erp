<?php

namespace App\CRM\Services;

use App\CRM\DTO\Lead\LeadDetailDTO;
use App\CRM\DTO\Lead\LeadDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadListResponseDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadRequestDTO;
use App\CRM\DTO\OpenAPI\Lead\LeadUpdateRequestDTO;
use App\CRM\Enums\LeadStatus;
use App\CRM\Hydrators\ClientHydrator;
use App\CRM\Hydrators\KanbanHydrator;
use App\CRM\Mapper\LeadMapper;
use App\Entity\Lead;
use App\Repository\ClientRepository;
use App\Repository\LeadRepository;
use App\Shared\Pagination\PaginationFactory;
use App\Storages\CRM\LeadStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class LeadService {

    public function __construct(
        private ClientRepository $clientRepository,
        private LeadMapper $leadMapper,
        private LeadRepository $leadRepository,
        private EntityManagerInterface $em,
        private LeadStorage $leadStorage,
        private KanbanHydrator $hydrator,
        private ClientHydrator $clientHydrator,
        private ClientService $clientService
    ) 
    {}

    public function getAllLead(Request $request): LeadListResponseDTO {
        $clientId = $request->query->get('client_id', null);
        if ($clientId !== null && !ctype_digit($clientId)) {
            throw new \InvalidArgumentException('client_id must be an integer');
        }
        $archive = $request->query->getBoolean('archive', false);
        $allCount = $this->leadRepository->getCountArchive();
        $pagination = PaginationFactory::create($request, $this->leadRepository, 12, $allCount);
        
        if(!$archive)
            $leads = $this->leadRepository->findAllWithClientAndResponsible($clientId, $pagination->offset(), $pagination->limit);
        else
            $leads = $this->leadRepository->findAllArchiveResponsible($pagination->offset(), $pagination->limit);
        
        $paginationDTO = $pagination->getPaginationDTO($leads);
        return $this->leadMapper->entityToListResponse($leads, $paginationDTO);
    }

    public function getDetailLead(int $id): LeadDetailDTO {
        
        $lead = $this->leadStorage->getLeadClientsDetail($id);
        $leadDTO = $this->leadMapper->entityToDetailResponse($lead);
        
        if($leadDTO->client != null) {
            $clientID = $leadDTO->client->id;
            $metrics = $this->clientRepository->getMetricsClient($clientID);
            $this->clientHydrator->hydrateMetricsClient($leadDTO->client, $metrics);
        }
        
        return $leadDTO;
    }

    public function createLead(LeadRequestDTO $request , bool $isPublicApi = false): LeadDTO {
        return $this->em->wrapInTransaction(function () use ($request, $isPublicApi) {
            $lead = new Lead();
            $this->hydrator->hydrateLead($lead, $request);
            $lead->setStatus(LeadStatus::ACTIVE);
            if($isPublicApi) {
                if($request->client) {
                    $client = $this->clientService->createClientLead($request->client, true);
                    $lead->setClient($client);
                }
                
            }
            else {
                if($lead->getClient() == null && $request->client) {
                    $client = $this->clientService->createClientLead($request->client, false);
                    $lead->setClient($client);
                }
            }
            $this->leadStorage->createLead($lead);

            return $this->leadMapper->entityToDTO($lead);
        });
    }

    public function updateLead(int $id, LeadUpdateRequestDTO $request): LeadDTO | array {
        return $this->em->wrapInTransaction(function () use ($request, $id) {
            if($request->isEmpty())
                return ["status" => "Empty body"];

            $lead = $this->leadStorage->getLead($id);
            $this->hydrator->hydrateLead($lead, $request);
            $this->leadStorage->updateLead($lead);

            return $this->leadMapper->entityToDTO($lead);
        });
    }

    public function deleteLead(int $id)
    {
        $lead = $this->leadStorage->getLead($id);
        $this->leadStorage->deleteLead($lead);
    }
}