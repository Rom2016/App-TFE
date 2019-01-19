<?php

namespace App\Repository;

use App\Entity\SectionPoints;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SectionPoints|null find($id, $lockMode = null, $lockVersion = null)
 * @method SectionPoints|null findOneBy(array $criteria, array $orderBy = null)
 * @method SectionPoints[]    findAll()
 * @method SectionPoints[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionPointsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SectionPoints::class);
    }

//    /**
//     * @return SectionPoints[] Returns an array of SectionPoints objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SectionPoints
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
