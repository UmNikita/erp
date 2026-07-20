<?php

namespace App\Tests;

use App\CRM\DTO\Client\ClientDetailDTO;
use App\CRM\Mapper\ClientMapper;
use App\CRM\Mapper\ContactMapper;
use App\CRM\Mapper\LeadMapper;
use App\Entity\Client;
use App\Entity\Lead;
use App\Repository\StageRepository;
use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CRMMappersTest extends TestCase
{
    public function testLeadToDetailResponse(): void
    {
        $reflectionClient = new ReflectionClass(Client::class);
        $propertyClient = $reflectionClient->getProperty('id');
        $client = new Client();
        $client->setName('ООО');
        $propertyClient->setValue($client, 1);

        $reflectionLead = new ReflectionClass(Lead::class);
        $propertyLead = $reflectionLead->getProperty('id');
        $lead = new Lead();
        $propertyLead->setValue($lead, 1);
        $lead->setName('Продажа сайта');
        $lead->setClient($client);

        $contactMapper = $this->createMock(ContactMapper::class);
        $clientMapper = $this->createMock(ClientMapper::class);
        $stageRepository = $this->createMock(StageRepository::class);

        $mapper = new LeadMapper(
            $stageRepository,
            $contactMapper,
            $clientMapper
        );

        $clientDTO = new ClientDetailDTO(
            1,
            'ООО',
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            new DateTime('now'),
            0, 0, 0, 0,
            null
        );

        $clientMapper->method('entityToDetailDTO')->willReturn($clientDTO);

        $dto = $mapper->entityToDetailResponse($lead);

        self::assertEquals('Продажа сайта', $dto->name);

        self::assertEquals('ООО', $dto->client->name);
    }
}
