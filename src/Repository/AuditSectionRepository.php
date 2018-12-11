<?php

namespace App\Repository;

use App\Entity\AuditSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditSection[]    findAll()
 * @method AuditSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditSectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditSection::class);
    }

//    /**
//     * @return AuditSection[] Returns an array of AuditSection objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AuditSection
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
