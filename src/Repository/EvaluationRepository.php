<?php

namespace App\Repository;

use App\Entity\Etudiant;
use App\Entity\Evaluation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evaluation>
 */
class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }
    public function findGroupEvaluationsByEtudiant(Etudiant $etudiant)
    {
        return $this->createQueryBuilder('e')
            ->join('e.matiere', 'm')
            ->where('m.semestre = :semestre')
            ->andWhere('e.statut_groupe = :statutGroupe')
            ->setParameter('semestre', $etudiant->getSemestre())
            ->setParameter('statutGroupe', 'groupe')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Evaluation[] Returns an array of Evaluation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Evaluation
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
