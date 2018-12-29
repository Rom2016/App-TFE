<?php

namespace App\Repository;

use App\Entity\AuditTests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditTests|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditTests|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditTests[]    findAll()
 * @method AuditTests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditTestsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditTests::class);
    }

//    /**
//     * @return AuditTests[] Returns an array of AuditTests objects
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
    public function findOneBySomeField($value): ?AuditTests
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
