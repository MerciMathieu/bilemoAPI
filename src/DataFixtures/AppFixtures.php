<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Client;
use App\Entity\Product;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        // Products
        for ($i = 0; $i < 20; $i++) {
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

//      Admin user
        $client = new Client();
        $client->setUsername('admin')
            ->setClientName('admin')
            ->setUrl("admin@test.fr")
            ->setPassword(password_hash('nimda', 'argon2i'))
            ->setRoles(['ROLE_ADMIN']);

        for ($i=0;$i<10;$i++) {
            $user = new User();
            $user->setUsername("user$i")
                ->setPassword("user$i")
                ->setRoles($user->getRoles())
                ->setClient($client);

            $manager->persist($user);
        }

        $manager->persist($client);

        $manager->flush();
    }
}
