<?php

namespace App\Repository;

use App\Entity\AuditTest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditTest|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditTest|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditTest[]    findAll()
 * @method AuditTest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditTestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditTest::class);
    }

//    /**
//     * @return AuditTest[] Returns an array of AuditTest objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AuditTest
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
