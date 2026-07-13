<?php

namespace App\CRM\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArchiveController extends AbstractController
{
    #[Route('/crm/archive', name: 'app_crm_archive')]
    public function index(): Response
    {
        return $this->render('services/crm/archive.html.twig');
    }
}
