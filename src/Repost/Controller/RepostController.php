<?php

namespace App\Repost\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RepostController extends AbstractController
{
    #[Route('/repost', name: 'app_repost')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::REPOST_ACCESS->value);
        return $this->render('services/repost/index.html.twig');
    }
}
