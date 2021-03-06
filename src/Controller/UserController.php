<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Pagination\PaginationFactory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @OA\Tag(name="Users")
 */
class UserController extends ExtendedAbstractController
{
    /**
     * @Route("/api/users", name="users", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=200,
     *   description="Returns the users' list, corresponding to a Client.",
     *   @Model(type=User::class, groups={"users_list"})
     * )
     * @Cache(maxage="1 hour", public=true)
     */
    public function getUsers(
        UserRepository $userRepository,
        SerializerInterface $serializer,
        PaginationFactory $paginationFactory,
        Request $request
    ): Response {
        /** @var Client $client */
        $client = $this->getUser();
        $query = $userRepository->findByClientQueryBuilder($client);

        $paginatedCollection = $paginationFactory->createCollection($query, $request, 'users', [], 5);

        $usersJson = $serializer->serialize(
            $paginatedCollection,
            'json',
            SerializationContext::create()->setGroups(['users_list'])
        );

        return new Response($usersJson, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="user_details", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=200,
     *   description="Returns the user's details.",
     *   @Model(type=User::class, groups={"user_details"})
     * )
     * @Cache(maxage="1 hour", public=true)
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

        return new Response($userJson, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/users", name="add_user", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @OA\RequestBody(
     *   description="Enter the user's email you want to add",
     *   @Model(type=User::class, groups={"add_user"}),
     *   required=true
     * )
     * @OA\Response(
     *   response=201,
     *   description="Add a user.",
     *   @Model(type=User::class, groups={"user_details"})
     * )
     */
    public function addClientUser(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        $client = $this->getUser();

        /** @var User $user */
        $user = $serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            DeserializationContext::create()->setGroups(['add_user'])
        );

        if ($errorMessages = $this->getValidationErrors($user)) {
            return $this->throwValidationErrors($user);
        }

        $user->setRoles($user->getRoles());
        $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));

        $client->addUser($user);
        $manager->flush();

        $userJson = $serializer->serialize(
            $user,
            'json',
            SerializationContext::create()->setGroups(['user_details'])
        );

        return new Response($userJson, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/users/{id<\d+>}", name="delete_user", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=204,
     *   description="Remove a user"
     * )
     */
    public function deleteClientUser(User $user, EntityManagerInterface $manager): Response
    {
        $client = $this->getUser();

        if ($user->getClient()->getId() !== $client->getId()) {
            throw $this->createAccessDeniedException();
        }

        $client->removeUser($user);
        $manager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
