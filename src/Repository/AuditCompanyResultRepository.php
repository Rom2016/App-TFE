<?php

namespace App\Repository;

use App\Entity\AuditCompanyResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditCompanyResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditCompanyResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditCompanyResult[]    findAll()
 * @method AuditCompanyResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditCompanyResultRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditCompanyResult::class);
    }

//    /**
//     * @return AuditCompanyResult[] Returns an array of AuditCompanyResult objects
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
    public function findOneBySomeField($value): ?AuditCompanyResult
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
