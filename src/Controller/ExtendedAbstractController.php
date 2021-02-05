<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    protected function cacheInit(Response $response, Request $request): Response
    {
        $response->setEtag(md5($response->getContent()))
            ->setPublic()
            ->setMaxAge(3600)
            ->isNotModified($request);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
