<?php

namespace App\CRM\Services;

use App\Entity\IntegrationToken;
use App\Repository\IntegrationTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

class IntegrationService {

    public function __construct(
        private EntityManagerInterface $em,
        private IntegrationTokenRepository $repository
    ) 
    {}

    public function appendRequest(string $token) {
        $integrationToken = $this->repository->findOneBy(['token' => str_replace('Bearer ', '', $token)]);
        if (!$integrationToken) {
            throw new \RuntimeException('Token not found');
        }

        $integrationToken->setCountRequests($integrationToken->getCountRequests()+1);
        $this->em->persist($integrationToken);
    }

    public function createToken(): IntegrationToken {
        $token = new IntegrationToken();
        $token->setToken($this->generateToken());
        $token->setIsActive(true);
        $token->setCountRequests(0);
        $this->em->persist($token);
        $this->em->flush();
        return $token;
    }

    public function generateToken(): string
    {
        return 'rh_live_' . bin2hex(random_bytes(32));
    }
}

