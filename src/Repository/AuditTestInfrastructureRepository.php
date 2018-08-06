<?php

namespace App\Repository;

use App\Entity\AuditTestInfrastructure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditTestInfrastructure|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditTestInfrastructure|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditTestInfrastructure[]    findAll()
 * @method AuditTestInfrastructure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditTestInfrastructureRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditTestInfrastructure::class);
    }

//    /**
//     * @return AuditTestInfrastructure[] Returns an array of AuditTestInfrastructure objects
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
    public function findOneBySomeField($value): ?AuditTestInfrastructure
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
