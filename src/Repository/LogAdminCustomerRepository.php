<?php

namespace App\Repository;

use App\Entity\LogAdminCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LogAdminCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogAdminCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogAdminCustomer[]    findAll()
 * @method LogAdminCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogAdminCustomerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogAdminCustomer::class);
    }

//    /**
//     * @return LogAdminCustomer[] Returns an array of LogAdminCustomer objects
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
    public function findOneBySomeField($value): ?LogAdminCustomer
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
