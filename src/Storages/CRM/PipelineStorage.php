<?php

namespace App\Storages\CRM;

use App\Entity\Pipeline;
use App\Repository\PipelineRepository;
use App\Storages\CRMStorage;
use App\Storages\KeyType;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;

class PipelineStorage extends CRMStorage
{
    public function __construct(
        private EntityManagerInterface $em,
        private PipelineRepository $pipelineRepository,
        #[Autowire(service: 'crm.cache')]
        CacheInterface $cache
    ) {
        parent::__construct($cache, $em);
    }

    /**
     * @var Collection<int, Pipeline>
     */
    public function getPipelinesWithStages(): array
    {
        $pipelines = $this->rememberCache(KeyType::PIPELINES_DETAIL, 2592000, function () {
            return $this->pipelineRepository->findAllWithStages();
        });
        return $pipelines;
    }

    public function createOrUpdatePipeline(Pipeline $pipeline)
    {
        $this->persistRecord($pipeline);
        $this->forgetCache(KeyType::PIPELINES_DETAIL);
    }

    public function deletePipeline(Pipeline $pipeline)
    {
        $this->removeRecord($pipeline);
        $this->forgetCache(KeyType::PIPELINES_DETAIL);
    }

    public function getPipeline(int $id, bool $lock = false): Pipeline
    {
        if($lock)
            $pipeline = $this->pipelineRepository->find($id, LockMode::PESSIMISTIC_WRITE);
        else
            $pipeline = $this->pipelineRepository->find($id);
        $this->hasError($pipeline, 'Pipeline');
        return $pipeline;
    }
}