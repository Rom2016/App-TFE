<?php

namespace App\Repository;

use App\Entity\CompanyInfrastructure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CompanyInfrastructure|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyInfrastructure|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyInfrastructure[]    findAll()
 * @method CompanyInfrastructure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyInfrastructureRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CompanyInfrastructure::class);
    }

//    /**
//     * @return CompanyInfrastructure[] Returns an array of CompanyInfrastructure objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompanyInfrastructure
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
