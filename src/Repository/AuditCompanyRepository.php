<?php

namespace App\Repository;

use App\Entity\AuditCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditCompany[]    findAll()
 * @method AuditCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditCompanyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditCompany::class);
    }

//    /**
//     * @return AuditCompany[] Returns an array of AuditCompany objects
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
    public function findOneBySomeField($value): ?AuditCompany
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
