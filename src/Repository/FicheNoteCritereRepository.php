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
