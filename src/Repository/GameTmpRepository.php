<?php

namespace App\Repository;

use App\Entity\GameTmp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameTmp>
 *
 * @method GameTmp|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameTmp|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameTmp[]    findAll()
 * @method GameTmp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameTmpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameTmp::class);
    }
    public function add(GameTmp $gameTmp, bool $flush = false): void
    {
        $this->getEntityManager()->persist($gameTmp);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
