<?php

namespace App\Repository;

use App\Entity\Etudiant;
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

    public function findAllMatiere() : array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findMatiereByEtudiant(Etudiant $user)
    {
        return $this->createQueryBuilder('m')
            ->where('m.semestre = :semestre')  // On filtre les matières par le même semestre que l'étudiant
            ->setParameter('semestre', $user->getSemestre())
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findMatiereBySemestre(Etudiant $user)
    {
        return $this->createQueryBuilder('m')
            ->where('m.semestre = :semestre')
            ->setParameter('semestre', $user->getSemestre())
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findMatiereAndNoteByEtudiant(Etudiant $user)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.Evaluation', 'e') // joint les évaluations
            ->leftJoin('e.notes', 'n') // joint les notes

            ->addSelect('e', 'n')

            ->where('m.semestre = :semestre') // lien avec le semestre
            ->setParameter('semestre', $user->getSemestre())

            ->andWhere('n.Etudiant = :etudiant OR n.id IS NULL') // lien avec l'étudiant meme si les notes sont null
            ->setParameter('etudiant', $user->getId())

            ->orderBy('m.nom', 'ASC') //trie

            ->getQuery()
            ->getResult();
    }



//    public function findAllEvaluationByEtudiant($user)
//    {
//        return $this->createQueryBuilder('n')
//            ->join('n.Evaluation', 'e')
//
//            ->join('e.matiere', 'm')
//
//            ->addSelect('e', 'm')
//
//            ->where('n.Etudiant = :user')
//            ->setParameter('user', $user)
//
//            ->getQuery()
//            ->getResult();
//    }


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
