<?php

namespace App\CRM\RestAPIController\v1;

use App\CRM\DTO\OpenAPI\Contact\ContactRequestDTO;
use App\CRM\DTO\OpenAPI\Contact\ContactUpdateRequestDTO;
use App\CRM\Mapper\ContactMapper;
use App\CRM\RestAPIController\APIController;
use App\CRM\Services\ClientService;
use App\CRM\Services\ContactService;
use App\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;

#[Route('/crm')]
final class ContactController extends APIController
{
    #[Route('/client/{id}/contacts', methods: ['GET'])]
    #[OA\Get(
        summary: 'Получить список контактов клиента',
        tags: ['CRM / Contact'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список контактов',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ContactListResponse'
                )
            )
        ]
    )]
    public function index(ContactRepository $contactRepository, ContactMapper $contactMapper): Response
    {
        $contacts = $contactRepository->findAll();
        $contactsDTO = $contactMapper->entityToListResponse($contacts);
        return $this->response($contactsDTO);
    }

    #[Route('/contact', methods: ['POST'])]
    #[OA\Post(
        summary: 'Создать нового контакта',
        tags: ['CRM / Contact'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/ContactRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Новый контакт создан',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Contact'
                )
            )
        ]
    )]
    public function create(Request $request, ContactService $contactService): Response
    {
        $contactRequest = $this->serializeRequest($request, ContactRequestDTO::class);
        $contactResponse = $contactService->createContact($contactRequest);
        return $this->response($contactResponse, Response::HTTP_CREATED);
    }

    #[Route('/contact/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Обновить контакт',
        tags: ['CRM / Contact'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/ContactRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Контакт изменен',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Contact'
                )
            )
        ]
    )]
    public function update(int $id, Request $request, ContactService $contactService): Response
    {
        $contactRequest = $this->serializeRequest($request, ContactUpdateRequestDTO::class);
        $contactResponse = $contactService->updateContact($contactRequest, $id);
        return $this->response($contactResponse);
    }

    #[Route('/contact/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Удалить контакт',
        tags: ['CRM / Contact'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Клиент удален'
            )
            
        ]
    )]
    public function delete(int $id, ContactService $contactService): Response
    {
        $contactService->deleteContact($id);
        return $this->response(["status" => "Контакт успешно удален"], 204);
    }
}