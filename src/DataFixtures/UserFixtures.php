<?php
// filepath: src/DataFixtures/UserFixtures.php
namespace App\DataFixtures;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Bundle\MongoDBBundle\Fixture\ODMFixtureInterface;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;

class UserFixtures implements ODMFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $dm = $manager instanceof DocumentManager ? $manager : null;
        if (!$dm) {
            throw new \InvalidArgumentException('Expected DocumentManager instance.');
        }

        $faker = Factory::create();

        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->setName($faker->name());
            $user->setEmail($faker->unique()->email());
            $user->setPassword('$2y$13$Rh0D6dNHKVzyKcOGfzPHj.Wcbj4mAmvtupHYyjeTKiJN4Zxz4mWVe');

            $dm->persist( $user);
        }

        $dm->flush();
    }
}