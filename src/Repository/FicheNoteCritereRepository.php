<?php

namespace App\Repository;

use App\Entity\FicheNoteCritere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheNoteCritere>
 */
class FicheNoteCritereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheNoteCritere::class);
    }

    public function findNoteByEtudiantAndCritere(int $etudiantId, int $critereId): ?Note
    {
        return $this->createQueryBuilder('n')
            ->join('n.etudiant', 'e')
            ->join('n.critere', 'c')
            ->where('e.id = :etudiantId')
            ->andWhere('c.id = :critereId')
            ->setParameters([
                'etudiantId' => $etudiantId,
                'critereId' => $critereId
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Dans FicheNoteCritereRepository
    public function findNoteCriteresByEtudiantAndEvaluation($userId, $evaluationId)
    {
        return $this->createQueryBuilder('fnc')
            ->join('fnc.Critere', 'c')
            ->join('c.Grille', 'g')
            ->join('App\Entity\FicheGrille', 'fg', 'WITH', 'fg.Grille = g AND fg.Etudiant = fnc.Etudiant')
            ->where('fnc.Etudiant = :user')
            ->andWhere('fg.Evaluation = :evaluation')
            ->setParameter('user', $userId)
            ->setParameter('evaluation', $evaluationId)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return FicheNoteCritere[] Returns an array of FicheNoteCritere objects
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

    //    public function findOneBySomeField($value): ?FicheNoteCritere
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
