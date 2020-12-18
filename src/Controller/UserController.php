<?php

namespace App\Controller;

use App\Classes\ExceptionHandler;
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
    public function getUsers(PlatformRepository $platformRepository, UserRepository $userRepository, int $platformId, SerializerInterface $serializer, ExceptionHandler $exception): Response
    {
        $platform = $platformRepository->find($platformId);
        if (!$platform || $platform === null) {
            $exception->throwJsonException("Platform $platformId was not found");
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
    public function getUserDetails(UserRepository $userRepository, SerializerInterface $serializer, int $userId, int $platformId, PlatformRepository $platformRepository, ExceptionHandler $exception): Response
    {
        $platform = $platformRepository->find($platformId);
        if (!$platform || $platform === null) {
            $exception->throwJsonException("Platform $platformId was not found");
        }

        $user = $userRepository->findBy(['platform' => $platform, 'id' => $userId]);
        if (!$user || $user === null) {
            $exception->throwJsonException("User $userId was not found");
        }

        $userJson = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'list_users_details']
        );

        return new Response($userJson, 200, ['Content-Type' => 'application/json']);
    }
}
