<?php

namespace App\DataFixtures;

use App\Entity\Platform;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $platform = new Platform();
        for ($i = 0; $i < 5; $i++) {
            $platform->setName("platform$i")
                ->setUrl("www.testurl.platform$i.com");

            $manager->persist($platform);
        }


        $manager->flush();
    }
}
