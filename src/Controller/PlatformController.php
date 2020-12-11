<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PlatformRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        return new Response($platformsJson);
    }

    /**
     * @Route("/bilemo/platforms/{platformId<\d+>}/users/create", name="user_create", methods={"POST"})
     */
    public function addUser(UserRepository $userRepository, SerializerInterface $serializer, int $platformId, PlatformRepository $platformRepository, Request $request, EntityManagerInterface $manager, ValidatorInterface $validator): Response
    {
        if (!$request->get('email')) {
            return new Response("Pour ajouter un utilisateur, vous devez entrer son email en paramètre.\nExemple: /users/create?EMAIL=TEST@EMAIL.FR", 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $email = $request->get('email');

        $platform = $platformRepository->find($platformId);
        if (!$platform || $platform == null ) {
            $errorMessage = "L'identifiant de la plateforme est inconnu";
            return new Response($errorMessage,400, [
                'Content-Type' => 'application/json'
            ]);
        }

        $newUser = new User();
        $newUser->setEmail($email);
        $errors = $validator->validate($newUser);

        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }

        $platform->addUser($newUser);
        $manager->flush();
        $user = $userRepository->findOneBy(['email' => $email]);

        $userJson = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'list_users']
        );

        return new Response("L'utilisateur a bien été ajouté.\nVoici ses informations: " . $userJson, 200, ['Content-Type' => 'application/json']);
    }
}
