<?php

namespace App\Repository;

use App\Entity\Sell; // Change to your Sell entity
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sell|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sell|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sell[]    findAll()
 * @method Sell[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sell::class);
    }

    public function save(Sell $sell): void 
    {
        $em = $this->getEntityManager();
        $em->persist($sell);
        $em->flush();
    }

    public function delete(Sell $sell): void 
    {
        $em = $this->getEntityManager();
        $em->remove($sell);
        $em->flush();
    }
}
