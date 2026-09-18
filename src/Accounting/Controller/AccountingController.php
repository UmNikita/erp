<?php

namespace App\Accounting\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountingController extends AbstractController
{
    #[Route('/accounting', name: 'app_accounting')]
    #[Route('/accounting/{vueRoute}', name: 'app_accounting_route', requirements: ['vueRoute' => '.*'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::ACCOUNTING_ACCESS->value);

        return $this->render('services/accounting/index.html.twig');
    }
}
