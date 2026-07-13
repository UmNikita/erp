<?php

namespace App\CRM\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RobotsController extends AbstractController
{
    #[Route('/crm/robots', name: 'app_crm_robots')]
    public function index(): Response
    {
        return $this->render('services/crm/robots.html.twig');
    }
}
