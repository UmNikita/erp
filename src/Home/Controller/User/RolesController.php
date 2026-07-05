<?php

namespace App\Home\Controller\User;

use App\Entity\Role;
use App\Home\Form\NewNameFormType;
use App\Security\Enum\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class RolesController extends AbstractController
{
    #[Route('/home/users/{id}/roles', name: 'app_home_users_roles')]
    public function edit()
    {
       $this->denyAccessUnlessGranted(Permission::ROOT_ACCESS->value);
    }

    #[Route('/home/users/roles/new', name: 'app_home_users_roles_new')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted(Permission::ROOT_ACCESS->value);
        
        $role = new Role();
        $form = $this->createForm(NewNameFormType::class, $role);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($role);
            $em->flush();
            return $this->redirectToRoute('app_home_users');
        }
        
        return $this->render('services/home/user/roles/new.html.twig', [
            "form" => $form
        ]);
    }
}
