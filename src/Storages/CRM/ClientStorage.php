<?php

namespace App\Storages\CRM;

use App\Entity\Client;
use App\Event\CRM\ClientUpdateEvent;
use App\Repository\ClientRepository;
use App\Repository\LeadRepository;
use App\Storages\CRMStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ClientStorage extends CRMStorage
{
    public function __construct(
        private EntityManagerInterface $em,
        private ClientRepository $clientRepository,
        private Security $security,
        private EventDispatcherInterface $eventDispatcher,
        private LeadRepository $leadRepository,
        #[Autowire(service: 'crm.cache')]
        CacheInterface $cache
    ) {
        parent::__construct($cache, $em);
    }

    private Client $receivedClient;

    public function getClient(int $id): Client
    {
        $client = $this->clientRepository->find($id);
        $this->hasError($client, 'Client');
        $this->receivedClient = clone $client;
        return $client;
    }

    public function updateClient(Client $client)
    {
        $this->persistRecord($client);
        $manager = $this->security->getUser();
        $event = new ClientUpdateEvent($client, $this->receivedClient, $manager);
        $this->eventDispatcher->dispatch($event);
    }
    
    public function createClient(Client $client)
    {
        $this->persistRecord($client);
    }

    public function deleteClient(Client $client)
    {
        return $this->em->wrapInTransaction(function () use ($client) {
            $this->leadRepository->unbindClient($client);
            $this->removeRecord($client);
        });
    }
}
