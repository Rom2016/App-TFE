<?php

namespace App\Repository;

use App\Entity\TestSelections;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TestSelections|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestSelections|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestSelections[]    findAll()
 * @method TestSelections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestSelectionsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TestSelections::class);
    }

//    /**
//     * @return TestSelections[] Returns an array of TestSelections objects
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
    public function findOneBySomeField($value): ?TestSelections
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
