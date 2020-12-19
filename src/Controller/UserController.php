<?php

namespace App\Controller;

use App\Repository\PlatformRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends ExtendedAbstractController
{
    /**
     * @Route("/bilemo/platforms/{platformId<\d+>}/users", name="users", methods={"GET"})
     */
    public function getUsers(
        int $platformId,
        PlatformRepository $platformRepository,
        UserRepository $userRepository,
        SerializerInterface $serializer
    ): Response
    {
        $platform = $platformRepository->find($platformId);
        if (!$platform || $platform === null) {
            return $this->throwJsonNotFoundException("Platform $platformId was not found");
        }

        $users = $userRepository->findBy(['platform' => $platform]);

        $usersJson = $serializer->serialize(
            $users,
            'json',
            ['groups' => 'list_users']
        );

        return new Response($usersJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/bilemo/platforms/{platformId<\d+>}/users/{userId<\d+>}", name="user_details", methods={"GET"})
     */
    public function getUserDetails(
        int $userId,
        int $platformId,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        PlatformRepository $platformRepository
    ): Response
    {
        $platform = $platformRepository->find($platformId);
        if (!$platform || $platform === null) {
            return $this->throwJsonNotFoundException("Platform $platformId was not found");
        }

        $user = $userRepository->findBy(['platform' => $platform, 'id' => $userId]);
        if (!$user || $user === null) {
            return $this->throwJsonNotFoundException("User $userId was not found");
        }

        $userJson = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'list_users_details']
        );

        return new Response($userJson, 200, ['Content-Type' => 'application/json']);
    }
}
