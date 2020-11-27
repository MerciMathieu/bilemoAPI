<?php

namespace App\DataFixtures;

use App\Entity\Platform;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Products
        for ($p = 0; $p < 5; $p++) {
            $product = new Product();
            $product->setColor('black')
                ->setDescription("description_$p")
                ->setMemory(512)
                ->setModelName("model_$p")
                ->setModelRef("Ref_$p")
                ->setPrice(4000.99)
                ->setUrlImage('');

            $manager->persist($product);
        }

        // Platforms
        for ($i = 0; $i < 5; $i++) {
            $platform = new Platform();

            $platform->setName("platform_$i")
                ->setUrl("www.testurl.platform$i.com");

            $manager->persist($platform);
        }

        $manager->flush();
    }
}
