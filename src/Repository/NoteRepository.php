<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function findNoteByEvaluation($id)
    {
        return $this->createQueryBuilder('note')
            ->where('note.Evaluation = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function findAllDetails($evaluation, $user)
    {
        return $this->createQueryBuilder('n')
            ->where('n.Evaluation = :evaluation')
            ->setParameter('evaluation', $evaluation)
            ->andWhere('n.Etudiant = :etudiant')
            ->setParameter('etudiant', $user)
            ->getQuery()
            ->getResult();
    }


    public function findNoteByMin($id)
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.note', 'ASC')
            ->where('n.Evaluation = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNoteByMax($id)
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.note', 'DESC')
            ->where('n.Evaluation = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNoteByMoyenne($id)
    {
        $result = $this->createQueryBuilder('n')
            ->select('AVG(n.note) as moyenne')
            ->where('n.Evaluation = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult();

        return round($result, 2);
    }

    public function findNoteByEtudiantMatiere($etudiantId, $matiereId)
    {
        $result = $this->createQueryBuilder('n')
            ->select('AVG(n.note) as moyenne')
            ->join('n.Evaluation', 'e')  // Join avec la table Evaluation
            ->where('e.Matiere = :matiereId')  // Filtrer par matière
            ->andWhere('n.Etudiant = :etudiantId')  // Filtrer par étudiant
            ->setParameter('matiereId', $matiereId)
            ->setParameter('etudiantId', $etudiantId)
            ->getQuery()
            ->getSingleScalarResult();

        return round($result, 2);
    }

    //    /**
    //     * @return Note[] Returns an array of Note objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Note
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
