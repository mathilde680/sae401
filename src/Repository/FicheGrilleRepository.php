<?php

namespace App\Repository;

use App\Entity\FicheGrille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheGrille>
 */
class FicheGrilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheGrille::class);
    }

    public function findCriteresByEtudiantAndEvaluation($userId, $evaluationId)
    {
        return $this->createQueryBuilder('fg')
            ->select('c', 'fnc')
            ->join('App\Entity\Critere', 'c', 'WITH', 'c.Grille = fg.Grille')
            ->join('App\Entity\FicheNoteCritere', 'fnc', 'WITH', 'fnc.Critere = c.id')
            ->where('fg.Etudiant = :user')
            ->andWhere('fg.Evaluation = :evaluation')
            ->andWhere('fnc.Etudiant = :user')
            ->setParameter('user', $userId)
            ->setParameter('evaluation', $evaluationId)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return FicheGrille[] Returns an array of FicheGrille objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FicheGrille
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
