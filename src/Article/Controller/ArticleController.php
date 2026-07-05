<?php

namespace App\Article\Controller;

use App\Security\Enum\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'app_article')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Permission::ARTICLE_ACCESS->value);
        return $this->render('services/article/index.html.twig');
    }
}
