<?php

namespace App\Repository;

use App\Entity\Money;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/*
 * @method Money|null find($id, $lockMode = null, $lockVersion = null)
 * @method Money|null findOneBy(array $criteria, array $orderBy = null)
 * @method Money[]    findAll()
 * @method Money[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoneyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Money::class);
    }

    public function save(Money $category): void
    {
        $em = $this->getEntityManager();
        $em->persist($category);
        $em->flush();
    }
    public function delete(Money $category): void
    {
        $em = $this->getEntityManager();
        $em->remove($category);
        $em->flush();
    }

}
