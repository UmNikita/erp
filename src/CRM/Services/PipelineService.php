<?php

namespace App\CRM\Services;

use App\CRM\DTO\OpenAPI\Pipeline\PipelineRequestDTO;
use App\CRM\DTO\Pipeline\PipelineDTO;
use App\CRM\Mapper\PipelineMapper;
use App\Entity\Pipeline;
use App\Repository\PipelineRepository;
use App\Repository\StageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PipelineService {

    public function __construct(
        private EntityManagerInterface $em,
        private PipelineRepository $pipelineRepository,
        private StageRepository $stageRepository,
        private PipelineMapper $pipelineMapper
    ) 
    {}

    public function createPipeline(PipelineRequestDTO $dto): PipelineDTO {
        $pipeline = new Pipeline();
        $pipeline->setName($dto->name);
        $this->em->persist($pipeline);
        $this->em->flush();
        return $this->pipelineMapper->entityToDTO($pipeline);
    }

    public function updatePipeline(int $id, PipelineRequestDTO $pipelineDTO): PipelineDTO {
        $pipeline = $this->pipelineRepository->find($id);
        if (!$pipeline)
            throw new NotFoundHttpException('Pipeline not found!');
        $pipeline->setName($pipelineDTO->name);
        $this->em->persist($pipeline);
        $this->em->flush();
        return $this->pipelineMapper->entityToDTO($pipeline);
    }

    public function deletePipeline(int $pipelineID) {

        $pipeline = $this->pipelineRepository->find($pipelineID);

        if (!$pipeline)
            throw new NotFoundHttpException('Pipeline not found!');

        if ($this->stageRepository->hasByPipeline($pipeline))
            throw new BadRequestHttpException('The pipelines with the stages cannot be deleted!');

        $this->em->remove($pipeline);
        $this->em->flush();
    }
}