<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/*
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }


    public function findOneByUsers(User $userOne, User $userTwo): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->where('(c.userOne = :userOne AND c.userTwo = :userTwo) OR (c.userOne = :userTwo AND c.userTwo = :userOne)')
           ->setParameter('userOne', $userOne)
            ->setParameter('userTwo', $userTwo)
           ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function save(Conversation $conversation): void
    {
        $em = $this->getEntityManager();
        $em->persist($conversation);
        $em->flush();
    }

    public function delete(Conversation $conversation): void
    {
        $em = $this->getEntityManager();
        $em->remove($conversation);
        $em->flush();
    }
}
