<?php

namespace App\CRM\RestAPIController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

abstract class APIController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer
    ) {}

    protected function response(mixed $data, int $code = Response::HTTP_OK): Response {
        return new Response(
            $this->serializer->serialize($data, 'json'), $code ,[
                'Content-Type' => 'application/json'
            ]
        );
    }

    protected function serializeRequest(Request $request, string $className): object {
        return $this->serializer->deserialize(
            $request->getContent(),
            $className,
            'json'
        );
    }
}