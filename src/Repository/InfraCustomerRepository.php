<?php

namespace App\Repository;

use App\Entity\InfraCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method InfraCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfraCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfraCustomer[]    findAll()
 * @method InfraCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfraCustomerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InfraCustomer::class);
    }

//    /**
//     * @return InfraCustomer[] Returns an array of InfraCustomer objects
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
    public function findOneBySomeField($value): ?InfraCustomer
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
