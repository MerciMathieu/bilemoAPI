<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Annotations as OA;

class UserController extends ExtendedAbstractController
{
    /**
     * @Route("/api/users", name="users", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=200,
     *   description="Returns the users' list, corresponding to a Client."
     * )
     */
    public function getUsers(UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $client = $this->getUser();

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
     * @Route("/api/users/{id<\d+>}", name="user_details", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=200,
     *   description="Returns the user's details."
     * )
     */
    public function getUserDetails(User $user, SerializerInterface $serializer): Response
    {
        $client = $this->getUser();

        if ($user->getClient()->getId() !== $client->getId()) {
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
     * @Route("/api/users", name="user_create", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=200,
     *   description="Add an user."
     * )
     */
    public function addClientUser(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $manager,
        ValidatorInterface $validator
    ): Response {
        $client = $this->getUser();

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
