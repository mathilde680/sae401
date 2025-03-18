<?php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Etudiant>
 */
class EtudiantRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Etudiant) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findAllById(int $id)
    {
        return $this->createQueryBuilder('e')
            ->where('e.id = :id')
            ->setParameter('id', $id)

            ->getQuery()
            ->getResult()
            ;
    }
    public function findEtudiantsByMatiereId(int $matiereId): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e
            FROM App\Entity\Etudiant e
            JOIN App\Entity\Matiere m WITH e.semestre = m.semestre
            WHERE m.id = :matiereId'
        )->setParameter('matiereId', $matiereId);

        return $query->getResult();
    }

    /**
     * Trouve les étudiants regroupés par TP pour une matière donnée
     */
    public function findEtudiantsByTp($matiereId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e
        FROM App\Entity\Etudiant e
        JOIN App\Entity\Matiere m WITH e.semestre = m.semestre
        WHERE m.id = :matiereId
        ORDER BY e.TP'
        )->setParameter('matiereId', $matiereId);

        $etudiants = $query->getResult();

        // Restructurer les résultats pour avoir un tableau de TP avec leurs étudiants
        $tpGroups = [];
        foreach ($etudiants as $etudiant) {
            $tp = $etudiant->getTp();
            if (!isset($tpGroups[$tp])) {
                $tpGroups[$tp] = [];
            }
            $tpGroups[$tp][] = $etudiant;
        }

        return $tpGroups;
    }

    /**
     * Trouve les étudiants regroupés par TD pour une matière donnée
     */
    public function findEtudiantsByTd($matiereId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e
        FROM App\Entity\Etudiant e
        JOIN App\Entity\Matiere m WITH e.semestre = m.semestre
        WHERE m.id = :matiereId
        ORDER BY e.TD'
        )->setParameter('matiereId', $matiereId);

        $etudiants = $query->getResult();

        // Restructurer les résultats pour avoir un tableau de TD avec leurs étudiants
        $tdGroups = [];
        foreach ($etudiants as $etudiant) {
            $td = $etudiant->getTd();
            if (!isset($tdGroups[$td])) {
                $tdGroups[$td] = [];
            }
            $tdGroups[$td][] = $etudiant;
        }

        return $tdGroups;
    }

    /**
     * Trouve les étudiants regroupés par Promotion pour une matière donnée
     */
    public function findEtudiantsByPromotion($matiereId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e
        FROM App\Entity\Etudiant e
        JOIN App\Entity\Matiere m WITH e.semestre = m.semestre
        WHERE m.id = :matiereId
        ORDER BY e.promotion'
        )->setParameter('matiereId', $matiereId);

        $etudiants = $query->getResult();

        // Restructurer les résultats pour avoir un tableau de Promotions avec leurs étudiants
        $promotionGroups = [];
        foreach ($etudiants as $etudiant) {
            $promotion = $etudiant->getPromotion();
            if (!isset($promotionGroups[$promotion])) {
                $promotionGroups[$promotion] = [];
            }
            $promotionGroups[$promotion][] = $etudiant;
        }

        return $promotionGroups;
    }

    /**
     * Compte le nombre d'étudiants par type de groupe pour une matière
     */
    public function countEtudiantsByTypeGroupe($typeGroupe, $matiereId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT COUNT(e.id) as count, e.' . strtolower($typeGroupe) . ' as groupKey
        FROM App\Entity\Etudiant e
        JOIN App\Entity\Matiere m WITH e.semestre = m.semestre
        WHERE m.id = :matiereId
        GROUP BY e.' . strtolower($typeGroupe)
        )->setParameter('matiereId', $matiereId);

        return $query->getResult();
    }

    //    /**
    //     * @return Etudiant[] Returns an array of Etudiant objects
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

    //    public function findOneBySomeField($value): ?Etudiant
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
