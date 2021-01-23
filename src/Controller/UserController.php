<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends ExtendedAbstractController
{
    /**
     * @Route("/api/clients/{id<\d+>}/users", name="users", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function getUsers(Client $client, UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        if ($client->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $users = $userRepository->findBy(['client' => $client]);
        $usersJson = $serializer->serialize(
            $users,
            'json',
            SerializationContext::create()->setGroups(
                ['users_list']
            )
        );

        return new Response($usersJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/clients/{client_id<\d+>}/users/{user_id<\d+>}", name="user_details", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
    public function getUserDetails(Client $client, User $user, SerializerInterface $serializer): Response
    {
        if ($client->getId() !== $this->getUser()->getId() || $user->getClient()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $userJson = $serializer->serialize(
            $user,
            'json',
            SerializationContext::create()->setGroups(
                ['user_details']
            )
        );

        return new Response($userJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/clients/{id<\d+>}/users/create", name="user_create", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function addClientUser(
        Client $client,
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $manager,
        ValidatorInterface $validator
    ): Response {
        if ($client->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        if ($this->getValidationErrors($validator, $user)) {
            $errorMessages = $this->getValidationErrors($validator, $user);
            return new JsonResponse($errorMessages, 400);
        }

        $client->addUser($user);
        $manager->flush();

        return new Response('User created', 200, ['Content-Type' => 'application/json']);
    }
}
