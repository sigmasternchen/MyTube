<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    // /**
    //  * @return Video[] Returns an array of Video objects
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

    /*public function findByUploader(User $user): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.uploader_id = :val')
            ->setParameter('val', $user->getId())
            ->getQuery()
            ->getArrayResult()
        ;
    }*/

    public function save(Video $video)
    {
        $this->_em->persist($video);
        $this->_em->flush();
    }

    public function update()
    {
        $this->_em->flush();
    }

    public function delete(Video $video)
    {
        $this->_em->remove($video);
        $this->_em->flush();
    }
}
