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

    public function findNoteByMin()
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.note', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNoteByMax()
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.note', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNoteByMoyenne()
    {
        return $this->createQueryBuilder('n')
            ->select('AVG(n.note) as moyenne')
            ->getQuery()
            ->getSingleScalarResult();
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
