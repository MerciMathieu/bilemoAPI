<?php

namespace App\Controller;

use App\Repository\PlatformRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/bilemo/{platformId}/users", name="users", methods={"GET"})
     */
    public function getUsers(PlatformRepository $platformRepository, UserRepository $userRepository, int $platformId, SerializerInterface $serializer): Response
    {
        $platform = $platformRepository->find($platformId);
        $users = $userRepository->findBy(['platform' => $platform]);

        $usersJson = $serializer->serialize(
            $users,
            'json',
            ['groups' => 'list_users']
        );

        return new Response($usersJson, 200, ['Content-Type' => 'application/json']);

        // REGARDER COMMENT SONT GÉRÉS METADATA
    }

    /**
     * @Route("/bilemo/users/{id}", name="user_details", methods={"GET"})
     */
    public function getUserDetails(UserRepository $userRepository, SerializerInterface $serializer, int $id): Response
    {
        $user = $userRepository->findOneBy(['id'=>$id]);
        $userJson = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'list_users']
        );

        return new Response($userJson, 200, ['Content-Type' => 'application/json']);
    }
}
