<?php

namespace App\Repository;

use App\Entity\LinkSelectInfra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LinkSelectInfra|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkSelectInfra|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkSelectInfra[]    findAll()
 * @method LinkSelectInfra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkSelectInfraRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LinkSelectInfra::class);
    }

//    /**
//     * @return LinkSelectInfra[] Returns an array of LinkSelectInfra objects
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
    public function findOneBySomeField($value): ?LinkSelectInfra
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
