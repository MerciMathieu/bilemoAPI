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
     * @Route("/bilemo/platforms/{platformId<\d+>}/users", name="users", methods={"GET"})
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

        return new Response($usersJson);

        // REGARDER COMMENT SONT GÉRÉS METADATA
        // return $this->json($users);
    }

    /**
     * @Route("/bilemo/users/{userId<\d+>}", name="user_details", methods={"GET"})
     */
    public function getUserDetails(UserRepository $userRepository, int $userId): Response
    {
        $user = $userRepository->findOneBy(['id' => $userId]);

        return $this->json($user);
    }
}
