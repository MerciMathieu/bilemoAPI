<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends ExtendedAbstractController
{
    /**
     * @Route("/bilemo/clients/{clientId<\d+>}/users", name="users", methods={"GET"})
     */
    public function getUsers(ClientRepository $clientRepository, UserRepository $userRepository, int $clientId, SerializerInterface $serializer): Response
    {
        $client = $clientRepository->find($clientId);
        $users = $userRepository->findBy(['client' => $client]);

        $usersJson = $serializer->serialize($users,'json', [
            'groups' => 'users_list'
        ]);

        return new Response($usersJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/bilemo/clients/{clientId<\d+>}/users/{userId<\d+>}", name="user_details", methods={"GET"})
     */
    public function getUserDetails(UserRepository $userRepository, SerializerInterface $serializer, int $userId): Response
    {
        $user = $userRepository->find($userId);
        $userJson = $serializer->serialize($user,'json', [
            'groups' => 'user_details'
        ]);

        return new Response($userJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/bilemo/clients/{clientId<\d+>}/users/create", name="user_create", methods={"POST"})
     */
    public function addUser(
        int $clientId,
        SerializerInterface $serializer,
        ClientRepository $clientRepository,
        Request $request,
        EntityManagerInterface $manager,
        ValidatorInterface $validator): Response
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
