<?php

namespace App\Controller;

use App\Entity\Critere;
use App\Entity\Grille;
use App\Form\AjoutGrilleType;
use App\Repository\CritereRepository;
use App\Repository\GrilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GrilleController extends AbstractController
{
    #[Route('/professeur/grille', name: 'app_grille')]
    public function index(Security $security, GrilleRepository $grilleRepository): Response
    {
        $user = $security->getUser();
        $profId = $user->getId();

        $grilles = $grilleRepository->findAllByProfesseur($profId);


        return $this->render('grille/index.html.twig', [
            'grilles' => $grilles,
        ]);
    }

    #[Route('/professeur/grille/{id}', name: 'app_fiche_grille', requirements: ['id'=>'\d+'])]
    public function grille_fiche(int $id, GrilleRepository $grilleRepository, CritereRepository $critereRepository): Response
    {
        $grille = $grilleRepository->find($id);
        $criteres = $critereRepository->findCriteresByGrille($id);

        return $this->render('grille/fiche.html.twig', [
            'grille' => $grille,
            'criteres' => $criteres,
        ]);
    }

    #[Route('/professeur/grille/ajout', name: 'app_grille_ajout')]
    public function ajout_grille(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $grille = new Grille();

        $grille->setProfesseur($user);


        $form = $this->createForm(AjoutGrilleType::class, $grille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($grille);

            foreach ($grille->getCriteres() as $critere) {
                $critere->setGrille($grille);
                $entityManager->persist($critere);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_grille');
        }

        return $this->render('grille/ajout.html.twig', [
            'form_grille' => $form->createView(),
        ]);
    }


    //SUPPRESSION GRILLE
    #[Route('/grille/{id}/supprime', name: 'app_grille_supprime', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprime(int $id, Request $request, EntityManagerInterface $entityManager, GrilleRepository $grilleRepository, CritereRepository $critereRepository): Response
    {
        //$grille = $grilleRepository->find($id);
        $grille = $entityManager->getRepository(Grille::class)->find($id);

        if ($grille) {
            // Supprimer tous les critères liés à la grille
            $criteres = $critereRepository->findBy(['Grille' => $grille]);
            foreach ($criteres as $critere) {
                $entityManager->remove($critere);
            }

            $entityManager->remove($grille);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_grille');
    }

    //MODIFICATION d'une grille
    #[Route('/professeur/grille/{id}/modif', name: 'app_grille_modif', requirements: ['id' => '\d+'])]
    public function modif(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $grille = $entityManager->getRepository(Grille::class)->find($id);


        $form = $this->createForm(AjoutGrilleType::class, $grille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($grille);
            $entityManager->flush();

            return $this->redirectToRoute('app_accueil_prof', ['code' => $grille->getId()]);
        }

        return $this->render('grille/ajout.html.twig', [
            'form_grille' => $form->createView(),
            'id' => $id,
            'grille' => $form->getData(),
        ]);
    }

}
