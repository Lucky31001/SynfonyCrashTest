<?php

namespace App\Repository;

use App\Entity\OnSale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/*
 * @method OnSale|null find($id, $lockMode = null, $lockVersion = null)
 * @method OnSale|null findOneBy(array $criteria, array $orderBy = null)
 * @method OnSale[]    findAll()
 * @method OnSale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OnSaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OnSale::class);
    }

    public function save(OnSale $category): void
    {
        $em = $this->getEntityManager();
        $em->persist($category);
        $em->flush();
    }
    public function delete(OnSale $category): void
    {
        $em = $this->getEntityManager();
        $em->remove($category);
        $em->flush();
    }

    public function findByUserWithArticles($user)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.article', 'a')
            ->addSelect('a')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
