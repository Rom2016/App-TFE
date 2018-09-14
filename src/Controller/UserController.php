<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 29/03/2018
 * Time: 15:42
 */

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\AuditCompany;
use App\Entity\AuditPhase;
use App\Entity\AuditTestPhase;
use App\Entity\Company;
use App\Entity\Roles;
use App\Entity\UserRole;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserController extends AbstractController
{
    /**
     * Méthode qui gère la racine du serveur.
     * Elle se partage entre portail de connexion pour un utilisateur non authentifié et la page d'accueil pour un utilisateur authentifié.
     *
     * @Route("/connexion", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $error = $authenticationUtils->getLastAuthenticationError();

            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('user/login.html.twig', array(
                'last_username' => $lastUsername,
                'error'         => $error,
            ));
        }
        else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     *
     * @Route("/", name="homepage")
     */
    public function homepage()
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_company = $this->getDoctrine()->getRepository(Company::class);
        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);


        $array['nbUser'] = $repository_user->getNb();
        $array['nbCompany'] = $repository_company->getNb();
        $array['nbPhase'] = $repository_phase->getNb();
        $array['nbTest'] = $repository_test->getNb();

        return $this->render('user/homepage.html.twig', $array);
    }





    /**
     * Méthode qui gère la partie administration des utilisateurs
     *
     * @Route("/administration/utilisateur", name="admin_user")
     */
    public function viewAdminUser()
    {
        /**
             * Si l'utilisateur est connecté et qu'il a les droits admin, render le template d'admin
             */
                /**
                 * Si il y'a eu une soumission du formulaire pour un nouvel utilisateur depuis l'administration.
                 */
                if ($_POST){
                    switch ($_POST['submit']) {
                        case 'newUser':
                            $this->newUser();
                            break;
                    }
                }
                $repository = $this->getDoctrine()->getRepository(AppUser::class);
                $array['users'] = $repository->findAll();
                return $this->render('user/administration_user.html.twig', $array);
            /**
             * L'utilisateur n'a pas les droits admin, render le template d'accès refusé.
             */
        /**
         * L'utilisateur n'est pas connecté, redirigé vers le portail de connexion.
         */
    }

    /**
     * Méthode appelée dans la celle ci-dessus lors de la soumission d'un nouvel utilisateur
     */
    public function newUser()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_roles = $this->getDoctrine()->getRepository(Roles::class);
        $role = $repository_roles->findOneBy(['role'=>'ROLE_USER']);
        $pwd = password_hash('TF3_@UD1T-2018', PASSWORD_BCRYPT);
        $date = date('Y-m-d H:i:s');
        $date = new \DateTime($date);
        $user = new AppUser($_POST['email'], $pwd, $_POST['fName'],$_POST['sName'],$_POST['function'], $date, $role);
        $user_role = new UserRole($user, $role);
        /**
         * L'EntityManager de Doctrine s'occupe de faire la conversion objet->base de données relationnelle.
         */
        $entityManager->persist($user);
        $entityManager->persist($user_role);
        $entityManager->flush();
    }


    /**
     * Appelée par requête AJAX pour la suppression d'utilisateurs sur la page d'administration des utilisateurs.
     * La requête envoie l'ID par POST de chaque utilisateur coché dans la table.
     * La méthode boucle sur cette variable pour les récupérer et supprime l'utilisateur.
     *
     * @Route("/administration/utilisateur/supprimer-utilisateur", name="delete_user", methods="POST")
     */
    public function deleteUser()
    {

        if(isset($_POST)) {
            $entityManager = $this->getDoctrine()->getManager();
            $repository_roles = $this->getDoctrine()->getRepository(UserRole::class);
            $repository_audit = $this->getDoctrine()->getRepository(AuditCompany::class);

            $user = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(['id' => $_POST['id']]);
            $audit = $repository_audit->findBy(['owner' => $user]);
            if($audit){
                foreach ($audit as $key => $value){
                    $value->setOwner($this->getUser());
                    $entityManager->persist($value);
                }
            }
            $role = $repository_roles->findOneBy(['user'=>$user]);
            $entityManager->remove($role);
            $entityManager->remove($user);
            $entityManager->flush();
        }
        $array['id'] = $_POST['id'];
        return new JsonResponse($array);
    }


    /**
     * Méthode qui retourne la page profile et la modification des informations de l'utilisateur si il a soumis le formulaire de la page profile.
     *
     * @Route("/profile", name="view_profile_user")
     */
    public function viewProfile()
    {
            /**
             * Si le formulaire a été soumis.
             */
            if ($_POST) {
                $entityManager = $this->getDoctrine()->getManager();
                /**
                 * Récupère l'utilisateur
                 */
                $user = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(['id' => $this->getUser()->id]);
                /**
                 * Le met à jour avec les informations du formulaire et UPDATE
                 */
                $user->updateProfile();
                $entityManager->persist($user);
                $entityManager->flush();
                $array = $_SESSION['user']->getAll();
            }
            return $this->render('user/profile_user.html.twig');
    }
}
