<?php

namespace App\Repository;

use App\Entity\InfraCustomert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method InfraCustomert|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfraCustomert|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfraCustomert[]    findAll()
 * @method InfraCustomert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfraCustomertRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InfraCustomert::class);
    }

//    /**
//     * @return InfraCustomert[] Returns an array of InfraCustomert objects
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
    public function findOneBySomeField($value): ?InfraCustomert
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
