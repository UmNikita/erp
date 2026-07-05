<?php

namespace App\Home\Controller\User;

use App\Entity\Department;
use App\Home\Form\NewNameFormType;
use App\Security\Enum\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class DepartmentController extends AbstractController
{
    #[Route('/home/users/{id}/departments', name: 'app_home_users_departments')]
    public function edit()
    {
        $this->denyAccessUnlessGranted(Permission::ROOT_ACCESS->value);
    }

    #[Route('/home/users/departments/new', name: 'app_home_users_departments_new')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted(Permission::ROOT_ACCESS->value);
        
        $department = new Department();
        $form = $this->createForm(NewNameFormType::class, $department);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($department);
            $em->flush();
            return $this->redirectToRoute('app_home_users');
        }
        
        return $this->render('services/home/user/departments/new.html.twig', [
            "form" => $form
        ]); 
    }
}
