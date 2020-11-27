<?php

namespace App\Controller;

use App\Repository\PlatformRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/bilemo/{platform}/users", name="users", methods={"GET"})
     */
    public function getUsers(PlatformRepository $platformRepository, UserRepository $userRepository, string $platform): Response
    {
        $users = $userRepository->findBy(['platform' => $platformRepository->findOneBy(['name' => $platform])]);

        return $this->json($users);
    }

    /**
     * @Route("/bilemo/users/{id}", name="user_details", methods={"GET"})
     */
    public function getUserDetails(UserRepository $userRepository, int $id): Response
    {
        $user = $userRepository->findOneBy(['id'=>$id]);

        return $this->json($user);
    }
}
