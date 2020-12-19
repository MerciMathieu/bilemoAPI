<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PlatformRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlatformController extends ExtendedAbstractController
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
    public function addUser(
        int $platformId,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $manager,
        ValidatorInterface $validator
    ): Response
    {
        $platform = $manager->getRepository(PlatformRepository::class)->find($platformId);
        if (!$platform || $platform === null) {
            return $this->throwJsonNotFoundException("Platform $platformId was not found");
        }

        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        if ($errorMessages = $this->getValidationErrors($validator, $user)) {
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
    public function deleteUser(
        int $platformId,
        int $userId,
        EntityManagerInterface $manager
    ): Response
    {
        $platform = $manager->getRepository(PlatformRepository::class)->find($platformId);
        if (!$platform || $platform === null) {
            return $this->throwJsonNotFoundException("Platform $platformId was not found");
        }

        $user = $manager->getRepository(UserRepository::class)->find($userId);
        if (!$user || $user === null) {
            return $this->throwJsonNotFoundException("User $userId was not found");
        }

        $platform->removeUser($user);
        $manager->flush();

        return new Response(
            "$userId from platform $platformId was deleted.",
            200,
            ["ContentType" => "application/json"]
        );
    }
}
