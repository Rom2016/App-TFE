<?php

namespace App\Repository;

use App\Entity\AuditTestPhase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AuditTestPhase|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditTestPhase|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditTestPhase[]    findAll()
 * @method AuditTestPhase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditTestPhaseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AuditTestPhase::class);
    }

    public function getNb()
    {
        $qb = $this->createQueryBuilder('t');
        return $qb
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }


//    /**
//     * @return AuditTestPhase[] Returns an array of AuditTestPhase objects
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
    public function findOneBySomeField($value): ?AuditTestPhase
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
