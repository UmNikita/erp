<?php

namespace App\Task\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'app_task')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::TASK_ACCESS->value);
        return $this->render('services/task/index.html.twig');
    }
}
