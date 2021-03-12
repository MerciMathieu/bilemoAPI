<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ClientController extends ExtendedAbstractController
{
    /**
     * @Route("/api/clients", name="register", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ): Response {
        /** @var Client $client */
        $client = $serializer->deserialize(
            $request->getContent(),
            Client::class,
            'json'
        );

        if ($this->getValidationErrors($validator, $client)) {
            return $this->throwValidationErrors($validator, $client);
        }

        $client->setRoles($client->getRoles());
        $client->setPassword($passwordEncoder->encodePassword($client, $client->getPassword()));

        $entityManager->persist($client);
        $entityManager->flush();

        $clientJson = $serializer->serialize(
            $client,
            'json'
        );

        return new Response($clientJson, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }
}
