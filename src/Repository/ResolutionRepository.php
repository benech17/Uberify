<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Exercice;
use App\Entity\Resolution;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Resolution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resolution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resolution[]    findAll()
 * @method Resolution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResolutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resolution::class);
    }

    /** 
     * @return Resolution[] Returns an array of Resolution objects
     */
    public function findByUserAndExercice(Exercice $exercice, User $user)
    {
        $first ='r.exercice = ' . $exercice->getId();
        $second ='r.user = ' . $user->getId();
        
        return $this->createQueryBuilder('r')
            ->andWhere($first)
            ->andWhere($second)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }


    /** 
     * @return Resolution[] Returns an array of Resolution objects
     */
    public function findAllUserByExercice(Exercice $exercice)
    {
        $first ='r.exercice = ' . $exercice->getId();
        return $this->createQueryBuilder('r')
            ->andWhere($first)
            ->getQuery()
            ->getResult();
    }
      /** 
     * @return Resolution[] Returns an array of Resolution objects
     */
    public function findAllExerciceByUser(User $user)
    {
        $first ='r.user = ' . $user->getId();
        return $this->createQueryBuilder('r')
            ->andWhere($first)
            ->getQuery()
            ->getResult();
    }



   /* public function findAlreadyTried(Exercice $exercice, User $user): ?Resolution
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exercice = :$exercice')
            ->andWhere('r.user= :$user')
            ->getQuery()
            ->getResult();
    }*/
}
