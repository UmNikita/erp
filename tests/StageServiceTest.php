<?php

namespace App\Tests;

use App\CRM\DTO\OpenAPI\Stage\StageRequestDTO;
use App\CRM\DTO\Stage\StageUIDTO;
use App\CRM\Mapper\StageMapper;
use App\CRM\Services\StageService;
use App\Entity\Pipeline;
use App\Entity\Stage;
use App\Repository\LeadRepository;
use App\Repository\PipelineRepository;
use App\Repository\StageRepository;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class StageServiceTest extends TestCase
{
    public function testCreateStage(): void
    {
        $stageRepository = $this->createMock(StageRepository::class);
        $pipelineRepository = $this->createMock(PipelineRepository::class);
        $leadRepository = $this->createMock(LeadRepository::class);
        $stageMapper = $this->createMock(StageMapper::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $stageDTO = new StageRequestDTO('Тест', 'red', 12);

        $em->method('wrapInTransaction')->willReturnCallback(function (Closure $callback) {return $callback();});

        $pipeline = new Pipeline();
        $pipelineRepository->method('find')->willReturn($pipeline);

        $stageRepository->method('getMaxSequenceByPipeline')->willReturn(5);

        $stageMapper->expects($this->once())->method('mapRequestDTOToEntity')->with($stageDTO, $this->isInstanceOf(Stage::class),  $pipeline, 6);

        $stageMapper->method('entityToUIDTO')->willReturn(new StageUIDTO(1, 'Тест', 'red', 6, 12));

        $service = new StageService($em, $stageRepository, $pipelineRepository, $leadRepository, $stageMapper);
        $res = $service->createStage($stageDTO);

        self::assertInstanceOf(StageUIDTO::class, $res);
    }

    public function testChangePositionUp(): void
    {
        $stageRepository = $this->createMock(StageRepository::class);
        $pipelineRepository = $this->createMock(PipelineRepository::class);
        $leadRepository = $this->createMock(LeadRepository::class);
        $stageMapper = $this->createMock(StageMapper::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $pipeline = new Pipeline();

        $stage1 = new Stage();
        $stage1->setSequence(1);
        $stage2 = new Stage();
        $stage2->setSequence(2);
        $stage3 = new Stage();
        $stage3->setSequence(3);
        $stage4 = new Stage();
        $stage4->setSequence(4);

        $stage4->setPipeline($pipeline);

        $stageRepository->method('find')->willReturn($stage4);

        $stageRepository->method('findBetweenSequences')->willReturn([$stage2, $stage3, $stage4]);

        $em->expects($this->once())->method('flush');

        $service = new StageService($em, $stageRepository, $pipelineRepository, $leadRepository, $stageMapper);
        $service->changePosition(4, 2);

        self::assertEquals(3, $stage2->getSequence());
        self::assertEquals(4, $stage3->getSequence());
        self::assertEquals(2, $stage4->getSequence());
    }
}
