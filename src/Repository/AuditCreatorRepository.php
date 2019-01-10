<?php

namespace App\Repository;

use App\Entity\AuditCreator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditCreator|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditCreator|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditCreator[]    findAll()
 * @method AuditCreator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditCreatorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditCreator::class);
    }

//    /**
//     * @return AuditCreator[] Returns an array of AuditCreator objects
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
    public function findOneBySomeField($value): ?AuditCreator
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
