<?php

namespace App\Repository;

use App\Entity\Groupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Groupe>
 */
class GroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Groupe::class);
    }
    /**
     * Récupère les groupes d'une évaluation pour un étudiant spécifique
     * en fonction du type de groupe (TD, TP, promo)
     *
     * @param int $idEvaluation
     * @param int $idUser
     * @return array
     */
    public function findGroupeByEvaluation($idEvaluation, $idUser)
    {
        $qb = $this->createQueryBuilder('g');

        // Récupérons l'évaluation pour connaître son type
        $evaluation = $this->getEntityManager()
            ->getRepository('App\Entity\Evaluation')
            ->find($idEvaluation);

        // Récupérons l'étudiant
        $etudiant = $this->getEntityManager()
            ->getRepository('App\Entity\Etudiant')
            ->findOneBy(['id' => $idUser]);

        $typeGroupe = $evaluation->getTypeGroupe(); // renvoie "TD", "TP" ou "promo"

        // jointures pour récupérer les étudiants de chaque groupe
        $qb->select('g', 'fg', 'e')
            ->leftJoin('g.ficheGroupes', 'fg')
            ->leftJoin('fg.etudiant', 'e')
            ->where('g.evaluation = :idEvaluation')
            ->setParameter('idEvaluation', $idEvaluation);

        // En fonction du type de groupe, on filtre différemment
        if ($typeGroupe === 'TD') {
            $qb->andWhere('g.nom LIKE :groupeTD')
                ->setParameter('groupeTD', 'Groupe TD ' . $etudiant->getTD() . '%');
        } elseif ($typeGroupe === 'TP') {
            $qb->andWhere('g.nom LIKE :groupeTP')
                ->setParameter('groupeTP', 'Groupe TP ' . $etudiant->getTP() . '%');
        } else { // promo
            $qb->andWhere('g.nom LIKE :groupePromo')
                ->setParameter('groupePromo', 'Groupe promo ' . $etudiant->getPromotion() . '%');
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Groupe[] Returns an array of Groupe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Groupe
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
