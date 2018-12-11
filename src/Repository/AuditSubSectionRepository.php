<?php

namespace App\Repository;

use App\Entity\AuditSubSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditSubSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditSubSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditSubSection[]    findAll()
 * @method AuditSubSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditSubSectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditSubSection::class);
    }

//    /**
//     * @return AuditSubSection[] Returns an array of AuditSubSection objects
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
    public function findOneBySomeField($value): ?AuditSubSection
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
