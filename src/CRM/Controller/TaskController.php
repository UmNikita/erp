<?php

namespace App\CRM\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/crm/tasks', name: 'app_crm_tasks')]
    public function index(): Response
    {
        return $this->render('services/crm/tasks.html.twig');
    }
}
