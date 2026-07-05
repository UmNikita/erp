<?php

namespace App\CRM\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ClientController extends AbstractController
{
    #[Route('/crm/clients', name: 'app_crm_clients')]
    public function index(): Response
    {
        return $this->render('services/crm/client.html.twig');
    }
}
