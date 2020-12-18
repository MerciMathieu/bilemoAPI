<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PlatformRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlatformController extends AbstractController
{
    /**
     * @Route("/bilemo/platforms", name="platform", methods={"GET"})
     */
    public function getPlatformsList(PlatformRepository $platformRepository, SerializerInterface $serializer): Response
    {
        $platforms = $platformRepository->findAll();
        $platformsJson = $serializer->serialize(
            $platforms,
            'json',
            ['groups' => 'list_platforms']
        );

        return new Response($platformsJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/bilemo/platforms/{platformId<\d+>}/users/create", name="user_create", methods={"POST"})
     */
    public function addUser(SerializerInterface $serializer, int $platformId, PlatformRepository $platformRepository, Request $request, EntityManagerInterface $manager, ValidatorInterface $validator): Response
    {
        $platform = $platformRepository->find($platformId);
        if (!$platform || $platform == null) {
            $exception = $this->createNotFoundException("Platform $platformId was not found.");
            return new Response(
                $exception->getMessage(),
                $exception->getStatusCode(),
                ["ContentType" => "application/json"]
            );
        }

        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $violations = $validator->validate($user);

        if ($violations->count()) {
            $errorMessages = [];
            foreach ($violations as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse($errorMessages, 400);
        }

        $platform->addUser($user);
        $manager->flush();

        $userJson = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'list_users']
        );

        return new Response($userJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/bilemo/platforms/{platformId<\d+>}/users/delete/{userId<\d+>}", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser(PlatformRepository $platformRepository, int $platformId, UserRepository $userRepository, int $userId,  EntityManagerInterface $manager): Response
    {
        $platform = $platformRepository->find($platformId);
        if (!$platform || $platform === null) {
            $exception = $this->createNotFoundException("Platform $platformId was not found.");
            return new Response(
                $exception->getMessage(),
                $exception->getStatusCode(),
                ["ContentType" => "application/json"]
            );
        }

        $user = $userRepository->find($userId);
        if (!$user || $user === null) {
            $exception = $this->createNotFoundException("User $userId was not found.");
            return new Response(
                $exception->getMessage(),
                $exception->getStatusCode(),
                ["ContentType" => "application/json"]
            );
        }

        $platform->removeUser($user);
        $manager->flush();

        return new Response(
            "$userId from platform $platformId was deleted.", 200, ["ContentType" => "application/json"]
        );
    }
}
