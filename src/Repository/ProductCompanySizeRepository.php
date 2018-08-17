<?php

namespace App\Repository;

use App\Entity\ProductCompanySize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProductCompanySize|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductCompanySize|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductCompanySize[]    findAll()
 * @method ProductCompanySize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductCompanySizeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProductCompanySize::class);
    }

//    /**
//     * @return ProductCompanySize[] Returns an array of ProductCompanySize objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductCompanySize
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
