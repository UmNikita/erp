<?php

namespace App\CRM\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CRMController extends AbstractController
{
    #[Route('/crm', name: 'app_crm')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::CRM_ACCESS->value);

        return $this->render('services/crm/index.html.twig');
    }
}
