<?php

namespace App\Repository;

use App\Entity\ExtCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ExtCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExtCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExtCustomer[]    findAll()
 * @method ExtCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtCustomerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ExtCustomer::class);
    }

//    /**
//     * @return ExtCustomer[] Returns an array of ExtCustomer objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExtCustomer
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
