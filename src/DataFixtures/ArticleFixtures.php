<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5 ; $i++) {
            $article = new Article();
            $article->setTitle('Article ' . $i);
            $article->setContent('This is the content of article ' . $i);
            $article->setImage('https://example.com/image' . $i . '.jpg');
            $article->setFav(rand(0, 5));
            $article->setPrice(rand(10, 100));
            $article->setTva(20);
            $article->setCategory($this->getReference('category_' . $i));

            $manager->persist($article);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }

}
