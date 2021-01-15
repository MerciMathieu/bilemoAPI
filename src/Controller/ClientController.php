<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends ExtendedAbstractController
{
    /**
     * @Route("/api/register", name="register", methods={"POST"})
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /** @var Client $client */
        $client = $serializer->deserialize($request->getContent(), Client::class, 'json');
        $client->setRoles($client->getRoles());
        $client->setPassword($passwordEncoder->encodePassword($client, $client->getPassword()));

        if ($this->getValidationErrors($validator, $client)) {
            $errorMessages = $this->getValidationErrors($validator, $client);
            return new JsonResponse($errorMessages, 400);
        }

        $entityManager->persist($client);
        $entityManager->flush();

        return new Response("The client number '" . $client->getId() . "' was created.", 201, ['Content-Type' => 'application/json']);
    }
}
