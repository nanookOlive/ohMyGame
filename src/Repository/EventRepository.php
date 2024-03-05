<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Event;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }


    /**
     * Retourne un tableau d'Event ou User est guest or host
     *
     * @param User $user
     * @return Event[]|null Returns an array of Event objects
     */
    public function findByHostOrGuests(User $user = null): array|null
    {
        if ($user === null) return null;

        $qb = $this->createQueryBuilder('e')
            ->leftjoin('e.eventRequests', 'r')
            ->addSelect('r');

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->eq('e.host', ':user'),
                $qb->expr()->andX(
                    $qb->expr()->eq('r.status', ':accepted'),
                    $qb->expr()->eq('r.user', ':user')
                )
            )
        )
            ->setParameters(
                [
                    'user' => $user->getId(),
                    'accepted' => 'ACCEPTED'
                ]
            );
        // dd($qb->getDQL());
        return $qb->orderBy('e.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return Events with a future startAt field
     *
     * @param string $city
     * @return Event[] Returns an array of Event objects
     */
    public function findByFutureDate(string $city = null): array
    {
        $today = new \DateTimeImmutable('today', new \DateTimeZone("Europe/Paris"));

        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.startAt >= :today')
            ->setParameter('today', $today);

        if ($city) {
            $qb->andWhere('e.address LIKE :city')
                ->setParameter('city', '%' . $city);
        }

        return $qb->orderBy('e.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return Events with a passed startAt field
     *
     * @return Event[] Returns an array of Event objects
     */
    public function findByPassedDate(): array
    {
        $today = new \DateTimeImmutable('today', new \DateTimeZone("Europe/Paris"));
        return $this->createQueryBuilder('e')
            ->andWhere('e.startAt < :today')
            ->setParameter('today', $today)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
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

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
