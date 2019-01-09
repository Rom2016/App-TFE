<?php

namespace App\Repository;

use App\Entity\LogType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LogType|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogType|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogType[]    findAll()
 * @method LogType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogType::class);
    }

//    /**
//     * @return LogType[] Returns an array of LogType objects
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
    public function findOneBySomeField($value): ?LogType
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
