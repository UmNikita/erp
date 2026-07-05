<?php

namespace App\CRM\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IntegrationController extends AbstractController
{
    #[Route('/crm/integrations', name: 'app_crm_integrations')]
    public function index(): Response
    {
        return $this->render('services/crm/integrations.html.twig');
    }
}
