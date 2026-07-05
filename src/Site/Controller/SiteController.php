<?php

namespace App\Site\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SiteController extends AbstractController
{
    #[Route('/site', name: 'app_site')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::SITE_ACCESS->value);
        return $this->render('services/site/index.html.twig');
    }
}
