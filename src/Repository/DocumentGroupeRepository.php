<?php

namespace App\Repository;

use App\Entity\DocumentGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentGroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentGroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentGroupe[]    findAll()
 * @method DocumentGroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentGroupe::class);
    }

    // /**
    //  * @return DocumentGroupe[] Returns an array of DocumentGroupe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DocumentGroupe
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
