<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/bilemo/clients/{clientId<\d+>}/users/create", name="user_create", methods={"POST"})
     */
    public function addUser(SerializerInterface $serializer, int $clientId, ClientRepository $clientRepository, Request $request, EntityManagerInterface $manager, ValidatorInterface $validator): Response
    {
        $client = $clientRepository->find($clientId);

        if (!$client || $client == null ) {
            $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $violations = $validator->validate($user);

        if ($violations->count()) {
            $errorMessages = [];
            foreach ($violations as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse($errorMessages, 400);
        }

        $client->addUser($user);
        $manager->flush();

        $userJson = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'list_users']
        );

        return new Response($userJson, 200, ['Content-Type' => 'application/json']);
    }
}
