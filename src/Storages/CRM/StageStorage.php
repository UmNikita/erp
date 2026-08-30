<?php

namespace App\Storages\CRM;

use App\Entity\Stage;
use App\Repository\StageRepository;
use App\Storages\CRMStorage;
use App\Storages\KeyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;

class StageStorage extends CRMStorage
{
    public function __construct(
        private EntityManagerInterface $em,
        private StageRepository $stageRepository,
        #[Autowire(service: 'crm.cache')]
        CacheInterface $cache
    ) {
        parent::__construct($cache, $em);
    }

    public function createOrUpdateStage(Stage $stage)
    {
        $this->persistRecord($stage);
        $this->forgetCache(KeyType::PIPELINES_DETAIL);
    }

    public function updateStagesArray(array $stages)
    {
        foreach ($stages as $stage) {
            $this->em->persist($stage);    
        }
        $this->em->flush();
        $this->forgetCache(KeyType::PIPELINES_DETAIL);
    }

    public function deleteStage(Stage $stage)
    {
        $this->removeRecord($stage);
        $this->forgetCache(KeyType::PIPELINES_DETAIL);
    }

    public function getStage(int $id): Stage
    {
        $stage = $this->stageRepository->find($id);
        $this->hasError($stage, 'Stage');
        return $stage;
    }
}
