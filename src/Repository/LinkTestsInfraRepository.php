<?php

namespace App\Repository;

use App\Entity\LinkTestsInfra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LinkTestsInfra|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkTestsInfra|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkTestsInfra[]    findAll()
 * @method LinkTestsInfra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkTestsInfraRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LinkTestsInfra::class);
    }

//    /**
//     * @return LinkTestsInfra[] Returns an array of LinkTestsInfra objects
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
    public function findOneBySomeField($value): ?LinkTestsInfra
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
