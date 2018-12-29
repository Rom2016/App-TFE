<?php

namespace App\Repository;

use App\Entity\InfraSelection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method InfraSelection|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfraSelection|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfraSelection[]    findAll()
 * @method InfraSelection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfraSelectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InfraSelection::class);
    }

//    /**
//     * @return InfraSelection[] Returns an array of InfraSelection objects
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
    public function findOneBySomeField($value): ?InfraSelection
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
