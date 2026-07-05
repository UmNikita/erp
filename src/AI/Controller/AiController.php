<?php

namespace App\AI\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AiController extends AbstractController
{
    #[Route('/ai', name: 'app_ai')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::AI_ACCESS->value);
        return $this->render('services/ai/index.html.twig');
    }
}
