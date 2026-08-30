<?php

namespace App\Storages;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CRMStorage
{
    public function __construct(
        #[Autowire(service: 'crm.cache')]
        private CacheInterface $cache,
        private EntityManagerInterface $em
    )
    {}

    protected function forgetCache(KeyType $type)
    {
        $this->cache->delete($this->getKey($type));
    }

    protected function rememberCache(KeyType $type, int $expires, callable $callback): array
    {
        return $this->cache->get($this->getKey($type), function (ItemInterface $item) use ($expires, $callback) {
            $item->expiresAfter($expires);
            return $callback();
        });
    }

    protected function persistRecord(object $record)
    {
        $this->em->persist($record);
        $this->em->flush();
    }

    protected function removeRecord(object $record)
    {
        $this->em->remove($record);
        $this->em->flush();
    }

    protected function hasError(?object $record, string $name)
    {
        if (!$record)
            throw new NotFoundHttpException($name.' not found!');
    }

    private function getKey(KeyType $type) {
        switch($type) {
            case KeyType::PIPELINES_DETAIL: {
                return "crm.pipelines-detail";
            }
            case KeyType::RESPONSIBLES_DETAIL: {
                return "crm.responsibles";
            }
        }
    }
}
