<?php

namespace App\Repository;

use App\Entity\AuditPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditPermission[]    findAll()
 * @method AuditPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditPermissionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditPermission::class);
    }

//    /**
//     * @return AuditPermission[] Returns an array of AuditPermission objects
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
    public function findOneBySomeField($value): ?AuditPermission
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
