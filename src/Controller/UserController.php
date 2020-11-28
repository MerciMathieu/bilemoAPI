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

        return new Response($usersJson);

        // REGARDER COMMENT SONT GÉRÉS METADATA
        // return $this->json($users);
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
