<?php

namespace App\Home\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(UserRepository $userRepository): Response
    {
        $userCount = $userRepository->count([]);
        return $this->render('services/home/index.html.twig', [
            "userCount" => $userCount
        ]);
    }
}
