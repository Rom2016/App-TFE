<?php

namespace App\Repository;

use App\Entity\LogAudits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LogAudits|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogAudits|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogAudits[]    findAll()
 * @method LogAudits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogAuditsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogAudits::class);
    }

//    /**
//     * @return LogAudits[] Returns an array of LogAudits objects
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
    public function findOneBySomeField($value): ?LogAudits
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
