<?php

namespace App\Repository;

use App\Entity\LogAuditPerm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LogAuditPerm|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogAuditPerm|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogAuditPerm[]    findAll()
 * @method LogAuditPerm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogAuditPermRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogAuditPerm::class);
    }

//    /**
//     * @return LogAuditPerm[] Returns an array of LogAuditPerm objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LogAuditPerm
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
