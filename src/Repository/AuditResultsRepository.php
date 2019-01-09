<?php

namespace App\Repository;

use App\Entity\AuditResults;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditResults|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditResults|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditResults[]    findAll()
 * @method AuditResults[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditResultsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditResults::class);
    }

//    /**
//     * @return AuditResults[] Returns an array of AuditResults objects
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
    public function findOneBySomeField($value): ?AuditResults
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
