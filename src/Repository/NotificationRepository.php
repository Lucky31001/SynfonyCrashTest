<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/*
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $notification): void
    {
        $em = $this->getEntityManager();
        $em->persist($notification);
        $em->flush();
    }
    public function delete(Notification $notification): void
    {
        $em = $this->getEntityManager();
        $em->remove($notification);
        $em->flush();
    }
}
