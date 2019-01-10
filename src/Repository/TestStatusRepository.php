<?php

namespace App\Repository;

use App\Entity\TestStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TestStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestStatus[]    findAll()
 * @method TestStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestStatusRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TestStatus::class);
    }

//    /**
//     * @return TestStatus[] Returns an array of TestStatus objects
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
    public function findOneBySomeField($value): ?TestStatus
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
