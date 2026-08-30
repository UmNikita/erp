<?php

namespace App\DataFixtures;

use App\Entity\Lead;
use App\Factory\ClientFactory;
use App\Factory\ContactFactory;
use App\Factory\LeadFactory;
use App\Factory\LeadMessageFactory;
use App\Factory\PipelineFactory;
use App\Factory\StageFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
final class LoadTestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        echo "start\n";

        echo "pipelines\n";
        $this->createPipelines();
        
        echo "leads\n";
        $this->createLeads($manager);

        echo "clients\n";
        $this->createClients($manager);

        echo "contacts\n";
        $this->createContacts();

        

    }

    private function createPipelines(): void
    {
        $pipelines = PipelineFactory::createMany(5);

        foreach ($pipelines as $pipeline) {
            StageFactory::createMany(10, [
                'pipeline' => $pipeline,
            ]);
        }
    }

    private function createClients(ObjectManager $manager): void
    {
        for ($i = 0; $i < 9000; $i++) {
            ClientFactory::createMany(100);

            $manager->clear();
            gc_collect_cycles();

            if ($i % 100 === 0) {
                echo "clients: " . ($i * 100) . "\n";
            }
        }
    }

    private function createContacts(): void
    {
        ContactFactory::createMany(2000);
    }

    private function createLeads(ObjectManager $manager): void
    {
        for ($i = 0; $i < 8000; $i++) {
            $leads = LeadFactory::createMany(100);
            foreach ($leads as $value) {
                $this->createMessages($manager, $value);
            }
            unset($leads);
            $manager->clear();

            gc_collect_cycles();

            if ($i % 100 === 0) {
                echo "lead: " . ($i * 100) . "\n";
            }
        }
    }

    private function createMessages(ObjectManager $manager, Lead $lead): void
    {
        $messages = LeadMessageFactory::createMany(5, ['lead' => $lead]);

        unset($messages);

        $manager->clear();

        gc_collect_cycles();
    }
}