<?php

namespace App\Repository;

use App\Entity\Sistema;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sistema|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sistema|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sistema[]    findAll()
 * @method Sistema[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SistemaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sistema::class);
    }

    // /**
    //  * @return Sistema[] Returns an array of Sistema objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sistema
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
