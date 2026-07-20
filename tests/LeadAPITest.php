<?php

namespace App\Tests;

use App\Factory\LeadFactory;
use App\Factory\PipelineFactory;
use App\Factory\StageFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class LeadAPITest extends WebTestCase
{
    use ResetDatabase;

    public function testCreateLead(): void
    {
        $client = static::createClient();

        $pipeline = PipelineFactory::createOne();
        $stage = StageFactory::createOne(['pipeline' => $pipeline]);

        $client->request('POST', '/api/v1/crm/lead',
            [],[], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Test',
                'stage_id' => $stage->getId()
            ])
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('Test', $response['name']);
        $this->assertIsArray($response);
    }

    public function testUpdateLead(): void
    {
        $client = static::createClient();

        $pipeline = PipelineFactory::createOne();
        $stage = StageFactory::createOne(['pipeline' => $pipeline]);
        $lead = LeadFactory::createOne(['stage' => $stage, 'name'=>"Old"]);

        $client->request('PATCH', '/api/v1/crm/lead/'.$lead->getId(),
            [],[], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'New'
            ])
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('New', $response['name']);
        $this->assertIsArray($response);
    }
}
