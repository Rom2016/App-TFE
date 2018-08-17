<?php

namespace App\Repository;

use App\Entity\SolutionFeatures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SolutionFeatures|null find($id, $lockMode = null, $lockVersion = null)
 * @method SolutionFeatures|null findOneBy(array $criteria, array $orderBy = null)
 * @method SolutionFeatures[]    findAll()
 * @method SolutionFeatures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SolutionFeaturesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SolutionFeatures::class);
    }

//    /**
//     * @return SolutionFeatures[] Returns an array of SolutionFeatures objects
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
    public function findOneBySomeField($value): ?SolutionFeatures
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
