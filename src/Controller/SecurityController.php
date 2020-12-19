<?php

namespace App\Controller;

use App\Entity\AccessUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator): Response
    {
        $values = json_decode($request->getContent());
        if(!isset($values->username, $values->password)) {
            return new Response("You must enter username and password", 500, ['Content-Type' => 'application/json']);
        }

        $accessUser = new AccessUser();
        $accessUser->setUsername($values->username);
        $accessUser->setPassword($passwordEncoder->encodePassword($accessUser, $values->password));
        $accessUser->setRoles($accessUser->getRoles());

        $violations = $validator->validate($accessUser);
        if($violations->count()) {
            $errorMessages = [];
            foreach ($violations as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse($errorMessages, 400);
        }

        $entityManager->persist($accessUser);
        $entityManager->flush();

        return new Response("Access on API created", 201, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request)
    {
        $user = $this->getUser();

        $userdData = $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);

        return $userdData;
    }

    /**
     * @Route("/login_check", name="login_check", methods={"POST"})
     */
    public function login_check(Request $request)
    {
    }
}
