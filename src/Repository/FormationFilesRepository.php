<?php

namespace App\Repository;

use App\Entity\FormationFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormationFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormationFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormationFiles[]    findAll()
 * @method FormationFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormationFiles::class);
    }

    // /**
    //  * @return FormationFiles[] Returns an array of FormationFiles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FormationFiles
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
