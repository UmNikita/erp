<?php

namespace App\Task\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'app_task')]
    #[Route('/tasks/{vueRoute}', name: 'app_task_route', requirements: ['vueRoute' => '.*'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted(Permission::TASK_ACCESS->value);
        $page = trim((string) $request->attributes->get('vueRoute', ''), '/');
        if ($page === 'admin' || str_starts_with($page, 'admin/') || str_starts_with($page, 'duties/')) {
            $this->denyAccessUnlessGranted(Permission::TASK_ADMIN_ACCESS->value);
        }
        return $this->render('services/task/index.html.twig');
    }
}
