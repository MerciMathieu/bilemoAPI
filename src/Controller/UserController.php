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
     * @Route("/api/clients/{clientId<\d+>}/users", name="users", methods={"GET"})
     */
    public function getUsers(
        ClientRepository $clientRepository,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        int $clientId): Response
    {
        $client = $clientRepository->find($clientId);
        $users = $userRepository->findBy(['client' => $client]);

        if ($clientId !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $usersJson = $serializer->serialize($users, 'json', [
            'groups' => 'users_list'
        ]);

        return new Response($usersJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/clients/{clientId<\d+>}/users/{userId<\d+>}", name="user_details", methods={"GET"})
     */
    public function getUserDetails(
        ClientRepository $clientRepository,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        int $clientId,
        int $userId): Response
    {
        $user = $userRepository->find($userId);

        if ($clientId !== $this->getUser()->getId() || $user->getClient() !== $this->getUser()->getId())
        {
            throw $this->createAccessDeniedException();
        }

        $userJson = $serializer->serialize($user, 'json', [
            'groups' => 'user_details'
        ]);

        return new Response($userJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/clients/{clientId<\d+>}/users/create", name="user_create", methods={"POST"})
     */
    public function addClientUser(
        SerializerInterface $serializer,
        ClientRepository $clientRepository,
        Request $request,
        EntityManagerInterface $manager,
        ValidatorInterface $validator,
        int $clientId
    ): Response
    {
        if ($clientId !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        if ($this->getValidationErrors($validator, $user)) {
            $errorMessages = $this->getValidationErrors($validator, $user);
            return new JsonResponse($errorMessages, 400);
        }

        $client = $clientRepository->find($clientId);

        $user->setClient($client);
        $client->addUser($user);
        $manager->flush();

        return new Response('User created', 200, ['Content-Type' => 'application/json']);
    }
}
