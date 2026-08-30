<?php

namespace App\CRM\Services;

use App\CRM\DTO\Integration\IntegrationDTO;
use App\CRM\Mapper\IntegrationMapper;
use App\Entity\IntegrationToken;
use App\Repository\IntegrationTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IntegrationService {

    public function __construct(
        private EntityManagerInterface $em,
        private IntegrationTokenRepository $repository,
        private IntegrationMapper $mapper
    ) 
    {}

    private int $maxTokensCount = 12;

    public function appendRequest(string $token) {
        $integrationToken = $this->repository->findOneBy(['token' => str_replace('Bearer ', '', $token)]);
        if (!$integrationToken) {
            throw new \RuntimeException('Token not found');
        }

        $integrationToken->setCountRequests($integrationToken->getCountRequests()+1);
        $this->em->persist($integrationToken);
    }

    public function createToken(): IntegrationToken {
        $countTokens = $this->repository->getCount();
        if($countTokens == $this->maxTokensCount || $countTokens > $this->maxTokensCount) {
            throw new BadRequestHttpException('Achived limit tokens!');
        }
        $token = new IntegrationToken();
        $token->setToken($this->generateToken());
        $token->setIsActive(true);
        $token->setCountRequests(0);
        $this->em->persist($token);
        $this->em->flush();
        return $token;
    }

    public function updateToken(int $id): IntegrationDTO {
        $integration = $this->repository->find($id);
        if (!$integration)
            throw new NotFoundHttpException('Integration not found!');
        $integration->setToken($this->generateToken());
        $this->em->persist($integration);
        $this->em->flush();
        $response = $this->mapper->entityToDTO($integration);
        return $response;
    }

    public function setActiveToken(int $id, bool $active): IntegrationDTO {
        $integration = $this->repository->find($id);
        if (!$integration)
            throw new NotFoundHttpException('Integration not found!');
        $integration->setIsActive($active);
        $this->em->persist($integration);
        $this->em->flush();
        return $this->mapper->entityToDTO($integration);
    }

    private function generateToken(): string
    {
        return 'rh_live_' . bin2hex(random_bytes(32));
    }
}

