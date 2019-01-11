<?php

namespace App\Repository;

use App\Entity\LogAdminUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LogAdminUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogAdminUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogAdminUser[]    findAll()
 * @method LogAdminUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogAdminUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogAdminUser::class);
    }

//    /**
//     * @return LogAdminUser[] Returns an array of LogAdminUser objects
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
    public function findOneBySomeField($value): ?LogAdminUser
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
