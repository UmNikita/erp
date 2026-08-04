<?php

namespace App\CRM\RestAPIController;

use App\CRM\Exception\InvalidRequestException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class APIController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected ValidatorInterface $validator
    ) {}

    protected function response(mixed $data, int $code = Response::HTTP_OK): Response {
        return new Response(
            $this->serializer->serialize($data, 'json'), $code ,[
                'Content-Type' => 'application/json'
            ]
        );
    }

    protected function serializeRequest(Request $request, string $className): object {
        try {
            return $this->serializer->deserialize(
                $request->getContent(),
                $className,
                'json'
            );
        } 
        catch (NotNormalizableValueException $e) {
            throw new InvalidRequestException([$e->getPath() => ['Incorrect data type']]);
        } 
        catch (MissingConstructorArgumentsException $e) {
            throw new InvalidRequestException(['message' => 'Required fields are not filled in']);
        }
    }

    protected function validate(object $dto) {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => $this->getValidationErrors($errors)
            ], 422);
        }
    }

    private function getValidationErrors(ConstraintViolationListInterface $violations): array
    {
        $errors = [];

        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $errors;
    }
}