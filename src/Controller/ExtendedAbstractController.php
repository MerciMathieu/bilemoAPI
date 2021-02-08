<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
