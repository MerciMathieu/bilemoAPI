<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ExtendedAbstractController extends AbstractController
{
    protected function throwJsonNotFoundException(string $message = "Resource was not found"): Response
    {
        $exception = $this->createNotFoundException($message);

        return new Response(
            $exception->getMessage(),
            $exception->getStatusCode(),
            ["ContentType" => "application/json"]
        );
    }

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
}
