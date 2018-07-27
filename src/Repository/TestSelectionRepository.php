<?php

namespace App\Repository;

use App\Entity\TestSelection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TestSelection|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestSelection|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestSelection[]    findAll()
 * @method TestSelection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestSelectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TestSelection::class);
    }

//    /**
//     * @return TestSelection[] Returns an array of TestSelection objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TestSelection
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
