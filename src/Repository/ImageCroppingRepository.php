<?php

namespace App\Repository;

use App\Entity\ImageCropping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageCropping|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageCropping|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageCropping[]    findAll()
 * @method ImageCropping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageCroppingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageCropping::class);
    }

    // /**
    //  * @return ImageCropping[] Returns an array of ImageCropping objects
    //  */
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
    public function findOneBySomeField($value): ?ImageCropping
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
