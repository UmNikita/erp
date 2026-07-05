<?php

namespace App\Call\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CallController extends AbstractController
{
    #[Route('/call', name: 'app_call')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::CALL_ACCESS->value);
        return $this->render('services/call/index.html.twig');
    }
}
