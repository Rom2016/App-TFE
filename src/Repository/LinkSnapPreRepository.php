<?php

namespace App\Repository;

use App\Entity\LinkSnapPre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LinkSnapPre|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkSnapPre|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkSnapPre[]    findAll()
 * @method LinkSnapPre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkSnapPreRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LinkSnapPre::class);
    }

//    /**
//     * @return LinkSnapPre[] Returns an array of LinkSnapPre objects
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
    public function findOneBySomeField($value): ?LinkSnapPre
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
