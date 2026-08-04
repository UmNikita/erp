<?php

namespace App\Tests;

use App\Factory\LeadFactory;
use App\Factory\PipelineFactory;
use App\Factory\StageFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class KanbanGetTest extends WebTestCase
{
    use ResetDatabase;

    public function testGetKanban(): void
    {
        $client = static::createClient();

        $pipeline = PipelineFactory::createOne();
        $stage = StageFactory::createOne(['pipeline' => $pipeline, 'name' => 'TEST']);
        $lead = LeadFactory::createOne(['stage' => $stage, 'budget' => 75000, 'name' => 'TEST LEAD']);

        $client->request('GET', '/api/v1/crm/kanban/'.$pipeline->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(75000, $response['moneyAmount']);
        $this->assertCount(1, $response['stages']);
        $this->assertSame($stage->getName(), $response['stages'][0]['name']);
        $this->assertSame($lead->getName(), $response['stages'][0]['leads'][0]['name']);

        $this->assertIsArray($response);
    }
}