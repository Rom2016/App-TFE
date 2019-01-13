<?php

namespace App\Repository;

use App\Entity\LinkSnapSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LinkSnapSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkSnapSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkSnapSection[]    findAll()
 * @method LinkSnapSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkSnapSectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LinkSnapSection::class);
    }

//    /**
//     * @return LinkSnapSection[] Returns an array of LinkSnapSection objects
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
    public function findOneBySomeField($value): ?LinkSnapSection
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
