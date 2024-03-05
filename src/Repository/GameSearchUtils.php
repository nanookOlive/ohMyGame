<?php

namespace App\Repository;

use App\Entity\Editor;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\Collection;

class GameSearchUtils
{
    /**
     * Recherche Game par titre
     *
     * @param QueryBuilder $qb
     * @param string $title
     * @return void
     */
    public static function likeTitle(QueryBuilder $qb, string $title = null)
    {
        if ($title) {
            return $qb->andWhere('g.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }
    }

    /**
     * Recherche Game par Type
     *
     * @param QueryBuilder $qb
     * @param Collection $type
     * @return boolean
     */
    public static function hasType(QueryBuilder $qb, Collection $type = null)
    {
        if ($type->count() > 0) {
            $query = '';
            foreach ($type as $k => $t) {
                $query .=  $qb->andWhere(':type' . $k . ' MEMBER OF g.type')
                    ->setParameter('type' . $k, $t);
                $query .= ' ';
            }
            return $query;
        }
    }

    /**
     * Recherche Game par Theme
     *
     * @param QueryBuilder $qb
     * @param Collection $theme
     * @return boolean
     */
    public static function hasTheme(QueryBuilder $qb, Collection $theme = null)
    {
        if ($theme->count() > 0) {
            $query = '';
            foreach ($theme as $k => $t) {
                $query .= $qb->andWhere(':theme' . $k . ' MEMBER OF g.theme')
                    ->setParameter('theme' . $k, $t);
                $query .= ' ';
            }
            return $query;
        }
    }

    /**
     * Recherche Game par Editor
     *
     * @param QueryBuilder $qb
     * @param Editor $editor
     * @return boolean
     */
    public static function isEditor(QueryBuilder $qb, Editor $editor = null)
    {
        if ($editor) {
            return $qb->andWhere('g.editor = :editor')
                ->setParameter('editor', $editor);
        }
    }

    /**
     * Recherche Game par age
     *
     * @param QueryBuilder $qb
     * @param integer $age
     * @return boolean
     */
    public static function isMinimumAge(QueryBuilder $qb, int $age = null)
    {
        if ($age) {
            return $qb->andWhere('g.minimumAge >= :age')
                ->setParameter('age', $age);
        }
    }

    /**
     * Recherche Game par année
     *
     * @param QueryBuilder $qb
     * @param DatetimeImmutable $year
     * @return boolean
     */
    public static function isReleasedAt(QueryBuilder $qb, \DateTimeImmutable $year = null)
    {
        if ($year) {

            return $qb->andWhere('g.releasedAt >= :year')
                ->setParameter('year', $year);
        }
    }
}
