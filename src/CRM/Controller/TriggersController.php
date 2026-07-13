<?php

namespace App\CRM\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TriggersController extends AbstractController
{
    #[Route('/crm/triggers', name: 'app_crm_triggers')]
    public function index(): Response
    {
        return $this->render('services/crm/triggers.html.twig');
    }
}
