<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Exercice;
use App\Entity\ExerciceFiltre;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Exercice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exercice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exercice[]    findAll()
 * @method Exercice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercice::class);
    }

    /**
     * @return Exerice[]
     */
    public function findAllCreated($id):array{
        $arg='e.author = '.$id;
        return $this->createQueryBuilder('e')
            ->where($arg)
            ->getQuery()
            ->getResult();
    }

   
    /**
     * @return Exerice[]
     */
    public function findLatest():array{
        return $this->createQueryBuilder('p')
                ->orderBy('p.created_at','desc')
                ->setMaxResults(4)
                ->getQuery()
                ->getResult();

    }

    /**
     * @return Query
     */
    public function findAllQuery(ExerciceFiltre $search):Query{

        $query= $this->findVisibleQuery();
        if($search->getCategory()){
            $query=$query
                    ->andwhere('e.category = :category')
                    ->setParameter('category',$search->getCategory());
        }
        if($search->getMinDifficulte()){
            $query=$query
                    ->andwhere('e.difficulte >= :minDifficulte')
                    ->setParameter('minDifficulte',$search->getMinDifficulte());
        }
        if($search->getAppreciation()){
            if($search->getAppreaciation() == 1)
                $query=$query
                    ->orderBy('e.mean_feedback', 'ASC');
            else
                $query=$query
                    ->orderBy('e.mean_feedback', 'DESC');

        }
        return $query->getQuery();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */

    private function findVisibleQuery():QueryBuilder{
        return $this->createQueryBuilder('e')
                ->orderBy("e.difficulte", "DESC");

    }

    

}
