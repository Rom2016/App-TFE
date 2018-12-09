<?php

namespace App\Repository;

use App\Entity\MeetingRequests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MeetingRequests|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetingRequests|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetingRequests[]    findAll()
 * @method MeetingRequests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingRequestsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MeetingRequests::class);
    }

//    /**
//     * @return MeetingRequests[] Returns an array of MeetingRequests objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MeetingRequests
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
