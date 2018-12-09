<?php

namespace App\Repository;

use App\Entity\IntAuditPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method IntAuditPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntAuditPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntAuditPermission[]    findAll()
 * @method IntAuditPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntAuditPermissionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IntAuditPermission::class);
    }

//    /**
//     * @return IntAuditPermission[] Returns an array of IntAuditPermission objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IntAuditPermission
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
