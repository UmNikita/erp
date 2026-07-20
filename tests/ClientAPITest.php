<?php

namespace App\Tests;

use App\Factory\ClientFactory;
use App\Factory\ContactFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class ClientAPITest extends WebTestCase
{
    use ResetDatabase;

    public function testGetDetailClient(): void
    {
        $client = static::createClient();

        $clientMock = ClientFactory::createOne();
        $contactMock = ContactFactory::createOne(['client' => $clientMock]);

        $client->request('GET', '/api/v1/crm/client/'.$clientMock->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertCount(1, $response['contacts']);
        $this->assertSame($clientMock->getName(), $response['name']);
        $this->assertSame($contactMock->getName(), $response['contacts'][0]['name']);

        $this->assertIsArray($response);
    }
}
