<?php

namespace App\Partner\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PartnerController extends AbstractController
{
    #[Route('/partner', name: 'app_partner')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::PARTNER_ACCESS->value);
        return $this->render('services/partner/index.html.twig');
    }
}
