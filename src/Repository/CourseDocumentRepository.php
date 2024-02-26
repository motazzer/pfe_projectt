<?php

namespace App\Repository;

use App\Entity\CourseDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CourseDocument>
 *
 * @method CourseDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourseDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourseDocument[]    findAll()
 * @method CourseDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseDocument::class);
    }

//    /**
//     * @return CourseDocument[] Returns an array of CourseDocument objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CourseDocument
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
