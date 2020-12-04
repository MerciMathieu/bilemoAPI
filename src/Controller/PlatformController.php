<?php

namespace App\Controller;

use App\Repository\PlatformRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
}
