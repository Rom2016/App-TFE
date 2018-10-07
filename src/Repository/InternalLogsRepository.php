<?php

namespace App\Repository;

use App\Entity\InternalLogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method InternalLogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method InternalLogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method InternalLogs[]    findAll()
 * @method InternalLogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InternalLogsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InternalLogs::class);
    }

//    /**
//     * @return InternalLogs[] Returns an array of InternalLogs objects
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
    public function findOneBySomeField($value): ?InternalLogs
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
