<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Platform;
use App\Entity\Product;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        // Products
        for ($i = 0; $i < 5; $i++) {
            $product = new Product();
            $product->setColor($faker->safeColorName)
                ->setDescription($faker->realText(300, 2))
                ->setMemory($faker->randomElement([512, 1024, 2048]))
                ->setModelName($faker->word)
                ->setModelRef($faker->regexify('[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}'))
                ->setPrice($faker->randomFloat(2, 40, 1300))
                ->setUrlImage($faker->imageUrl(600, 400));

            $manager->persist($product);
        }

        // Platforms
        for ($i = 0; $i < 5; $i++) {
            $platform = new Platform();
            $platform->setName($faker->company)
                ->setUrl("www." . $platform->getName() . ".com");

            for ($u = 0; $u < 15; $u++) {
                $user = new User();
                $user->setEmail($faker->email);

                $platform->addUser($user);
            }

            $manager->persist($platform);
        }

        $manager->flush();
    }
}
