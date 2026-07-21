<?php

namespace App\Home\Controller\User;

use App\Home\Form\EditPasswordUserType;
use App\Home\Form\EditUserType;
use App\Home\Form\NewUserType;
use App\Home\Mapper\UserMapper;
use App\Home\PageData\UserPageData;
use App\Home\Service\UserService;
use App\Repository\DepartmentRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Security\Enum\Permission;
use App\Security\Service\RoleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class UsersHomeController extends AbstractController
{
    #[Route('/home/users', name: 'app_home_users')]
    public function index(UserPageData $userPage): Response
    {
        $this->denyAccessUnlessGranted(Permission::ROOT_ACCESS->value);
        
        $data = $userPage->getViewDataIndex();

        return $this->render('services/home/user/index.html.twig', $data);
    }

    #[Route('/home/users/new', name: 'app_home_users_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserMapper $userMapper, RoleRepository $roleRepository, DepartmentRepository $departmentRepository, UserService $userService): Response
    {
        $this->denyAccessUnlessGranted(Permission::ROOT_ACCESS->value);

        $form = $this->createForm(NewUserType::class, null, [
            'roles' => $roleRepository->findAll(),
            'departments' => $departmentRepository->findAll(),
        ]);

        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $user = $userMapper->formToEntity($data);
            $userService->createUser($user, $data['password']);

            return $this->redirectToRoute('app_home_users');
        }

        return $this->render('services/home/user/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/home/users/{id}/edit', name: 'app_home_users_edit')]
    public function edit(int $id, UserRepository $userRepository, Request $request, RoleRepository $roleRepository, DepartmentRepository  $departmentRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(Permission::ROOT_ACCESS->value);

        $user = $userRepository->getUserWithDepartment($id);
        $form = $this->createForm(EditUserType::class, $user, [
            'roles' => $roleRepository->findAll(),
            'departments' => $departmentRepository->findAll(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_home_users');
        }
        
        return $this->render('services/home/user/edit.html.twig', [
            "form" => $form
        ]);
    }

    #[Route('/home/users/{id}/password', name: 'app_home_users_password')]
    public function password(int $id, Request $request, UserRepository $userRepository, UserService $userService): Response
    {

        $this->denyAccessUnlessGranted(Permission::ROOT_ACCESS->value);

        $user = $userRepository->find($id);
        $form = $this->createForm(EditPasswordUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $userService->changePasswordUser($user, $data['password']);
            return $this->redirectToRoute('app_home_users');
        }

        return $this->render('services/home/user/password.html.twig', [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'form' => $form
        ]);
    }

    #[Route('/home/users/deactivate', name: 'app_home_users_deactivate', methods: ["POST"])]
    public function deactivate(Request $request, UserService $userService, UserRepository $userRepository) {
        
        $this->denyAccessUnlessGranted(Permission::ROOT_ACCESS->value);
        
        $data = json_decode($request->getContent(), true);
        $id = $data['id'] ?? null;
        if($id) {
            $user = $userRepository->find($id);
            if (!$user) {
                throw new NotFoundHttpException('User not found');
            }
            $userService->deactivateUser($user);
            return new JsonResponse([
                'success' => 'ok'
            ]);
        }
        else {
            throw new NotFoundHttpException('User not found');
        }
    }

    #[Route('/home/users/matrix/holder', methods: ["POST"])]
    public function matrixHolder(Request $request, RoleService $roleService) {

        $this->denyAccessUnlessGranted(Permission::ROOT_ACCESS->value);

        $data = json_decode($request->getContent(), true);
        
        $id = isset($data['id']) ? (int) $data['id'] : null;
        $name = $data['name'] ?? null;
        $isActive = $data['isActive'] ?? null;
        $isActive = filter_var($isActive, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        $roleService->changePermission($id, $name, $isActive);
        return new JsonResponse([
            'success' => 'ok'
        ]);
    }
}
