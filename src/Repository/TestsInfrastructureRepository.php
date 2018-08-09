<?php

namespace App\Repository;

use App\Entity\TestsInfrastructure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TestsInfrastructure|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestsInfrastructure|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestsInfrastructure[]    findAll()
 * @method TestsInfrastructure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestsInfrastructureRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TestsInfrastructure::class);
    }

//    /**
//     * @return TestsInfrastructure[] Returns an array of TestsInfrastructure objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TestsInfrastructure
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
