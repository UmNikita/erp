<?php

namespace App\Base\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BaseController extends AbstractController
{
    #[Route('/bases', name: 'app_base')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::BASE_ACCESS->value);
        return $this->render('services/base/index.html.twig');
    }
}
