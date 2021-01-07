<?php

namespace App\Repository;

use App\Entity\Video;
use App\Entity\View;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method View|null find($id, $lockMode = null, $lockVersion = null)
 * @method View|null findOneBy(array $criteria, array $orderBy = null)
 * @method View[]    findAll()
 * @method View[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, View::class);
    }

    // /**
    //  * @return View[] Returns an array of View objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?View
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function save(View $view)
    {
        $this->_em->persist($view);
        $this->_em->flush();
    }

    public function update()
    {
        $this->_em->flush();
    }

    public function countForVideo(Video $video): int
    {
        $qb = $this->createQueryBuilder("v");
        return $qb->select("count(v.id)")
            ->andWhere("v.video = :video")
            ->setParameter("video", $video->getId()->getBytes())
            ->andWhere($qb->expr()->isNotNull("v.validated"))
            ->getQuery()->getSingleScalarResult();
    }
}
