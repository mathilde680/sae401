<?php

namespace App\Repository;

use App\Entity\Matiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matiere>
 */
class MatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matiere::class);
    }

    public function findAllMatiereByProfesseur($profId): array
    {
        return $this->createQueryBuilder('m')
            ->join('App\Entity\FicheMatiere', 'fm', 'WITH', 'fm.Matiere = m.id')
            ->where('fm.Professeur = :profId')
            ->setParameter('profId', $profId)
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllBySemestreAndProfesseur($nom, $profId): array
    {
        return $this->createQueryBuilder('m')
            ->join('App\Entity\FicheMatiere', 'fm', 'WITH', 'fm.Matiere = m.id')
            ->where('m.nom LIKE :nom')
            ->andWhere('fm.Professeur = :profId')
            ->setParameter('nom', '%'.$nom.'%')
            ->setParameter('profId', $profId)
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }




    //    /**
    //     * @return Matiere[] Returns an array of Matiere objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Matiere
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
