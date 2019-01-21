<?php

namespace App\Repository;

use App\Entity\AppUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AppUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppUser[]    findAll()
 * @method AppUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AppUser::class);
    }

    public function getNb()
    {
        $qb = $this->createQueryBuilder('t');
        return $qb
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findNameContaining($key, $id) {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT * FROM app_user u
        WHERE u.first_name LIKE :price
        AND u.deactivated = FALSE 
        AND u.id != :id
        AND NOT EXISTS
            (SELECT * FROM user_permission p
            WHERE p.user_id = u.id)
        AND NOT EXISTS
            (SELECT * FROM audit_creator c
            WHERE c.creator_id = u.id)    
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['price' => $key.'%', 'id'=>$id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

//    /**
//     * @return AppUser[] Returns an array of AppUser objects
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
    public function findOneBySomeField($value): ?AppUser
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
