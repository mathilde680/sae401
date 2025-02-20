<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestType;
use App\Repository\EtudiantRepository;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_accueil_etudiant');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    //deconnexion
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    //page formulaire mdp oublié
    #[Route('/mdp_oublie', name: 'app_mdp_oublie')]
    public function forgotPassword(Request $request, EtudiantRepository $etudiantRepository, MailerService $mailerService): Response
    {
        $form = $this->createForm(ResetPasswordRequestType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //on cherche le user dans la base
            $etudiant = $etudiantRepository->findOneByEmail($form->get('email')->getData());

            //si l'etudiant existe dans la bdd
            if($etudiant){
                $mailerService->sendTemplatedMail(
                    destinataire: $etudiant->getEmail(),
                    sujet: 'Demande de réinitialisation du mot de passe',
                    template: 'resetPassword.html.twig', // Assurez-vous que ce template existe !
                );

                return new Response("Envoi Réussi ? Vérifiez avec l'URL localhost:1080");
            }

            else{
                $this->addFlash('error', 'Email ou adresse introuvable');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/resetPassword.html.twig', [
            'requestPasswordForm' => $form->createView(),
        ]);
    }

    //pour indiquer le new mdp
    #[Route('/reset_mdp', name: 'app_reset_mdp')]
    public function resetPassword(Request $request, EtudiantRepository $etudiantRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('newPassword');
            $confirmPassword = $request->request->get('newPasswordConf');

            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_reset_mdp');
            }

            // Vérifier si l'utilisateur existe en base
            $etudiant = $etudiantRepository->findOneBy(['email' => $email]);

            if (!$etudiant) {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cet email.');

            }

            // Hacher et sauvegarder le nouveau mot de passe
            $hashedPassword = $passwordHasher->hashPassword($etudiant, $password);
            $etudiant->setPassword($hashedPassword);

            $entityManager->persist($etudiant);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/newPassword.html.twig');
    }
}
