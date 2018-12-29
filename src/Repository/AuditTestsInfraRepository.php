<?php

namespace App\Repository;

use App\Entity\AuditTestsInfra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditTestsInfra|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditTestsInfra|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditTestsInfra[]    findAll()
 * @method AuditTestsInfra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditTestsInfraRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditTestsInfra::class);
    }

//    /**
//     * @return AuditTestsInfra[] Returns an array of AuditTestsInfra objects
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
    public function findOneBySomeField($value): ?AuditTestsInfra
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
