<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends AbstractController
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
        $client = $serializer->deserialize($request->getContent(), Client::class, 'json');

        $violations = $validator->validate($client);
        if ($violations->count()) {
            $errorMessages = [];
            foreach ($violations as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse($errorMessages, 400);
        }

        $entityManager->persist($client);
        $entityManager->flush();

        return new Response("The '" . $client->getPlatformName() . "' platform was created with an access account to the API.", 201, ['Content-Type' => 'application/json']);
    }
}
