<?php

namespace App\Statistic\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StatisticController extends AbstractController
{
    #[Route('/statistic', name: 'app_statistic')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::STATISTIC_ACCESS->value);
        return $this->render('services/statistic/index.html.twig');
    }
}
