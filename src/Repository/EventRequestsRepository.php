<?php

namespace App\Repository;

use App\Entity\EventRequests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventRequests>
 *
 * @method EventRequests|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventRequests|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventRequests[]    findAll()
 * @method EventRequests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRequestsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventRequests::class);
    }

    //    /**
    //     * @return EventRequests[] Returns an array of EventRequests objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EventRequests
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
