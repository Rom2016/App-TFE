<?php

namespace App\Repository;

use App\Entity\TestType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TestType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestType[]    findAll()
 * @method TestType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TestType::class);
    }

//    /**
//     * @return TestType[] Returns an array of TestType objects
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
    public function findOneBySomeField($value): ?TestType
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
