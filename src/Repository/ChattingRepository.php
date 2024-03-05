<?php

namespace App\Repository;

use App\Entity\Chatting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chatting>
 *
 * @method Chatting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chatting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chatting[]    findAll()
 * @method Chatting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChattingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chatting::class);
    }
    
    public function add(Chatting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
//    /**
//     * @return Chatting[] Returns an array of Chatting objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Chatting
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
