<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Type;
use App\Entity\Theme;
use App\Entity\Editor;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * Retourne un tableau de jeux aléatoires
     *
     * @param int $qty
     * @return Game[] Returns a random array of Game objects
     */
    public function findRandomGames(int $qty = 1): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = 'SELECT id FROM game ORDER BY RAND () LIMIT ' . $qty;
        $result = $connection->executeQuery($sql);
        // $r est un tableau associatif avec les propriétés de game
        $r = $result->fetchAllAssociative();
        // Ensuite on remplit $array avec les objets Game provenant de $r
        $array = [];
        foreach ($r as $g) {
            $array[] = $this->find($g['id']);
        }
        return $array;
    }
    public function add(Game $type, bool $flush=false){

        $this->getEntityManager()->persist($type);
        if($flush){
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupérer la liste des Jeux par ordre alphabétique
     * Contenant le therme de recherche $title
     * Dans la limite de 12
     *
     * @param string $searchTherm, optionnel therme de recherche sur le titre de Game
     * 
     * @return Game[] Returns an array of Game objects
     */
    public function findByTermOrdered(?string $searchTherm): array
    {
        $qb =  $this->createQueryBuilder('g');

        if (!empty($searchTherm)) {
            $qb->andWhere('g.title LIKE :searchTherm')
                ->setParameter('searchTherm', '%' . $searchTherm . '%');
        }

        return $qb->orderBy('g.title', 'ASC')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère la liste des jeux issus de la recherche
     *
     *
     * @return Game[] Returns an array of Game objects
     */
    public function findBySearchFields(
        string $title = null,
        Collection $type = null,
        Collection $theme = null,
        Editor $editor = null,
        int $age = null,
        \DateTimeImmutable $year = null
    ): array {
        // Create Query builder
        $qb = $this->createQueryBuilder('g');
        // search by title
        GameSearchUtils::likeTitle($qb, $title);
        // search by Type
        GameSearchUtils::hasType($qb, $type);
        // search by Theme
        GameSearchUtils::hasTheme($qb, $theme);
        // search by Editor
        GameSearchUtils::isEditor($qb, $editor);
        // search by age
        GameSearchUtils::isMinimumAge($qb, $age);
        // search by year
        GameSearchUtils::isReleasedAt($qb, $year);
        // results
        return $qb->orderBy('g.title', 'ASC')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $page
     * @return Paginator
     */
    public function findByAdminPage(int $page): Paginator
    {
        $query = $this->createQueryBuilder('g')
            ->orderBy('g.title', 'ASC')
            ->setFirstResult($page * 5)
            ->setMaxResults(12);

        $paginator = new Paginator($query);

        return $paginator;
    }

    //    /**
    //     * @return Game[] Returns an array of Game objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Game
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


}
