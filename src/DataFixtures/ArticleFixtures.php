<?php
namespace App\DataFixtures;

use App\Document\Article;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Bundle\MongoDBBundle\Fixture\ODMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures implements ODMFixtureInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $dm = $manager instanceof DocumentManager ? $manager : null;
        if (!$dm) {
            throw new \RuntimeException('Expected DocumentManager instance.');
        }

        $faker = Factory::create();

        // Fetch all users
        $users = $dm->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                $article = new Article();
                $article->setTitle($faker->sentence());
                $article->setContent($faker->paragraph());
                $article->setAuthor($user);

                $dm->persist($article);
            }
        }

        $dm->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}