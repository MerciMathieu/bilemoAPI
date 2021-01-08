<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/bilemo/clients/{clientId<\d+>}/users", name="users", methods={"GET"})
     */
    public function getUsers(ClientRepository $clientRepository, UserRepository $userRepository, int $clientId, SerializerInterface $serializer): Response
    {
        $client = $clientRepository->find($clientId);
        $users = $userRepository->findBy(['client' => $client]);

        $usersJson = $serializer->serialize(
            $users,
            'json',
            ['groups' => 'list_users']
        );

        return new Response($usersJson, 200, ['Content-Type' => 'application/json']);

        // REGARDER COMMENT SONT GÉRÉS METADATA
    }

    /**
     * @Route("/bilemo/clients/{clientId<\d+>}/users/{userId<\d+>}", name="user_details", methods={"GET"})
     */
    public function getUserDetails(UserRepository $userRepository, SerializerInterface $serializer, int $userId): Response
    {
        $user = $userRepository->find($userId);
        $userJson = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'list_users']
        );

        return new Response($userJson, 200, ['Content-Type' => 'application/json']);
    }
}
