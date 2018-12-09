<?php

namespace App\Repository;

use App\Entity\IntCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method IntCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntCustomer[]    findAll()
 * @method IntCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntCustomerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IntCustomer::class);
    }

//    /**
//     * @return IntCustomer[] Returns an array of IntCustomer objects
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
    public function findOneBySomeField($value): ?IntCustomer
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
