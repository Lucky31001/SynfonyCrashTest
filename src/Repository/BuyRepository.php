<?php

namespace App\Repository;

use App\Entity\Buy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Buy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Buy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Buy[]    findAll()
 * @method Buy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Buy::class);
    }

    public function save(Buy $buy): void
    {
        $em = $this->getEntityManager();
        $em->persist($buy);
        $em->flush();
    }

    public function delete(Buy $buy): void
    {
        $em = $this->getEntityManager();
        $em->remove($buy);
        $em->flush();
    }
}
