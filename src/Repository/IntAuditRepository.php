<?php

namespace App\Repository;

use App\Entity\IntAudit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method IntAudit|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntAudit|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntAudit[]    findAll()
 * @method IntAudit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntAuditRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IntAudit::class);
    }

//    /**
//     * @return IntAudit[] Returns an array of IntAudit objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IntAudit
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
