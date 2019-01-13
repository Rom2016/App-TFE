<?php

namespace App\Repository;

use App\Entity\LogAdminContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LogAdminContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogAdminContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogAdminContent[]    findAll()
 * @method LogAdminContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogAdminContentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogAdminContent::class);
    }

//    /**
//     * @return LogAdminContent[] Returns an array of LogAdminContent objects
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
    public function findOneBySomeField($value): ?LogAdminContent
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
