<?php

namespace App\Repository;

use App\Entity\FicheMatiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheMatiere>
 */
class FicheMatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheMatiere::class);
    }


    public function findAllBySemestreAndProfesseur($nom, $profId): array
    {
        return $this->createQueryBuilder('fm')
            ->join('fm.matiere', 'm')

            ->where('m.nom LIKE :nom')
            ->setParameter('nom', '%'.$nom.'%')

            ->andWhere('fm.Professeur = :profId')
            ->setParameter('profId', $profId)

            ->orderBy('m.nom', 'ASC')

            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return FicheMatiere[] Returns an array of FicheMatiere objects
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

    //    public function findOneBySomeField($value): ?FicheMatiere
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
