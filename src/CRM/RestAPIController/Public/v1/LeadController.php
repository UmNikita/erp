<?php

namespace App\CRM\RestAPIController\Public\v1;

use App\CRM\DTO\OpenAPI\Lead\LeadRequestDTO;
use App\CRM\RestAPIController\APIController;
use App\CRM\Services\IntegrationService;
use App\CRM\Services\LeadService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class LeadController extends APIController
{

    #[Route('/lead', methods: ['POST'])]
    public function create(Request $request, IntegrationService $integrationService, LeadService $leadService): Response
    {
        $token = $request->headers->get('Authorization');
        $integrationService->appendRequest($token);
        $dto = $this->serializeRequest($request, LeadRequestDTO::class);
        $errorResponse = $this->validate($dto);
        if ($errorResponse)
            return $errorResponse;
        $lead = $leadService->createLead($dto, true);
        return $this->response($lead, Response::HTTP_CREATED);
    }
}