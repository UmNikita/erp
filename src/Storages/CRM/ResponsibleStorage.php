<?php

namespace App\Storages\CRM;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Storages\CRMStorage;
use App\Storages\KeyType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;

class ResponsibleStorage extends CRMStorage
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        #[Autowire(service: 'crm.cache')]
        CacheInterface $cache
    ) {
        parent::__construct($cache, $em);
    }

    public function getResponsible(int $id): User
    {
        $responsible = $this->userRepository->find($id);
        $this->hasError($responsible, 'Responsible');
        return $responsible;
    }

    public function createResponsible(User $user)
    {
        $this->persistRecord($user);
        $this->forgetCache(KeyType::RESPONSIBLES_DETAIL);
    }

    /**
     * @var Collection<int, User>
     */
    public function getResponsibles(): array
    {
        $pipelines = $this->rememberCache(KeyType::RESPONSIBLES_DETAIL, 2592000, function () {
            return $this->userRepository->findAll();
        });
        return $pipelines;
    }
}
