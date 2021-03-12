<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ExtendedAbstractController extends AbstractController
{
    protected function getValidationErrors(ValidatorInterface $validator, $entity): array
    {
        $errorMessages = [];

        $violations = $validator->validate($entity);
        if ($violations->count()) {
            foreach ($violations as $error) {
                $errorMessages[] = $error->getMessage();
            }
        }

        return $errorMessages;
    }

    protected function throwValidationErrors(ValidatorInterface $validator, $entity): JsonResponse
    {
        $errorMessages = $this->getValidationErrors($validator, $entity);
        $response = [
            'status' => 'Exception',
            'code' => Response::HTTP_NOT_FOUND,
            'message' => $errorMessages
        ];

        return new JsonResponse($response, Response::HTTP_NOT_FOUND);
    }
}
