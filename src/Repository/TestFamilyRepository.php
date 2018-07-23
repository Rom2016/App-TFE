<?php

namespace App\Repository;

use App\Entity\TestFamily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TestFamily|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestFamily|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestFamily[]    findAll()
 * @method TestFamily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestFamilyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TestFamily::class);
    }

//    /**
//     * @return TestFamily[] Returns an array of TestFamily objects
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
    public function findOneBySomeField($value): ?TestFamily
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
