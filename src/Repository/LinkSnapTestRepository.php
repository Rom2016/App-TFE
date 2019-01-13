<?php

namespace App\Repository;

use App\Entity\LinkSnapTest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LinkSnapTest|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkSnapTest|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkSnapTest[]    findAll()
 * @method LinkSnapTest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkSnapTestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LinkSnapTest::class);
    }

//    /**
//     * @return LinkSnapTest[] Returns an array of LinkSnapTest objects
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
    public function findOneBySomeField($value): ?LinkSnapTest
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
