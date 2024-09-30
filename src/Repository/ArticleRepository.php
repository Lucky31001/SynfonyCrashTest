<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/*
 * @method ArticleForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleForm[]    findAll()
 * @method ArticleForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }
    public function save(Article $article): void
    {
        $em = $this->getEntityManager();
        $em->persist($article);
        $em->flush();
    }
}
