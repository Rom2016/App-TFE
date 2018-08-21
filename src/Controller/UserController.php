<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 29/03/2018
 * Time: 15:42
 */

namespace App\Controller;

use App\Entity\AuditPhase;
use App\Entity\AuditTestPhase;
use App\Entity\Company;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;


class UserController extends AbstractController
{
    /**
     * Méthode qui gère la racine du serveur.
     * Elle se partage entre portail de connexion pour un utilisateur non authentifié et la page d'accueil pour un utilisateur authentifié.
     *
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        /**
         * Test si l'utilisateur a cliqué sur le lien de déconnexion.
         * La déconnexion de l'utilisateur se passe lorsque GET['rq'] == deconnexion
         */
        if (isset($_GET['rq']) && $_GET['rq'] == 'deconnexion'){
            if(isset($_SESSION['user'])){
                session_destroy();
                return $this->render('user/login.html.twig');
            }else{
                return $this->render('user/login.html.twig');
            }
        }
        /**
         * Cas où l'utilisateur est déjà connecté.
         * Récupère les nombres qui sont affichés sur la page d'accueil dans la base de données.
         */
        elseif(isset($_SESSION['user'])){
            /**
             * Les repository sont des fichiers répertoriés dans le dossier Repository et permettent d'utiliser les méthodes Doctrine de base.
             * Ils sont créés automatiquement lors de la création de l'entité.
             */
            $repository_user = $this->getDoctrine()->getRepository(User::class);
            $repository_company = $this->getDoctrine()->getRepository(Company::class);
            $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
            $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
            $array = $_SESSION['user']->getAll();
            $array['nbUser'] = $repository_user->getNb();
            $array['nbCompany'] = $repository_company->getNb();
            $array['nbPhase'] = $repository_phase->getNb();
            $array['nbTest'] = $repository_test->getNb();
            return $this->render('user/homepage.html.twig',$array);
        }
        /**
         * Connecte l'utilisateur
         */
        elseif(isset($_POST['user']) && isset($_POST['pass'])){
            return $this->userConnection();
        }
        /**
         * Retourne le portail de connexion si aucun des autres cas n'est validé.
         */
        else{
            return $this->render('user/login.html.twig');
        }
    }


    public function userConnection()
    {
        $email = htmlspecialchars($_POST['user']);
        $psw = htmlspecialchars($_POST['pass']);
        /**
         * Données relatives au reCAPTCHA
         */
        $secret = "6Ld3lw4UAAAAAOfAaZ8SKgLfUbcMGx0fL8vRZbWp";
        $response = $_POST['g-recaptcha-response'];
        $remoteip = $_SERVER['REMOTE_ADDR'];
        $api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
            . $secret
            . "&response=" . $response
            . "&remoteip=" . $remoteip ;
        $decode = json_decode(file_get_contents($api_url), true);

        $repositoryU = $this->getDoctrine()->getRepository(User::class);
        $user = $repositoryU->findOneBy(['email' => $email]);
        /**
         * Si le reCAPTCHA a réussi.
         */
        if($decode['success'] == true) {
            /**
             * Si un utilisateur avec l'adresse rentré par le formulaire a été trouvé dans la base de données et que le mot de passe haché et salé de la même manière qu'à la création du compte concorde => Connexion
             */
            if ($user && password_verify($psw, $user->getPass())) {
                $user->startConnection();
                $repository_user = $this->getDoctrine()->getRepository(User::class);
                $repository_company = $this->getDoctrine()->getRepository(Company::class);
                $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
                $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
                $array = $_SESSION['user']->getAll();
                /**
                 * Même chose que la méthode homepage()
                 */
                $array['nbUser'] = $repository_user->getNb();
                $array['nbCompany'] = $repository_company->getNb();
                $array['nbPhase'] = $repository_phase->getNb();
                $array['nbTest'] = $repository_test->getNb();

                return $this->render('user/homepage.html.twig', $array);
                /**
                 * Mot de passe érroné
                 */
            } else {
                return $this->render('user/login.html.twig', ['title' => "Bienvenue", 'error' => 'Identifiants invalides']);
            }
        }else{
            return $this->render('user/login.html.twig', ['title' => "Bienvenue", 'error' => 'Erreur de captcha']);
        }
    }


    /**
     * Méthode qui gère la partie administration des utilisateurs
     *
     * @Route("/administration-utilisateur", name="admin_user")
     */
    public function viewAdminUser()
    {
        if(isset($_SESSION)){
            /**
             * Si l'utilisateur est connecté et qu'il a les droits admin, render le template d'admin
             */
            if ($_SESSION['user']->isAdmin) {
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
                $repository = $this->getDoctrine()->getRepository(User::class);
                $array = $_SESSION['user']->getAll();
                $array['users'] = $repository->findAll();
                return $this->render('user/administration_user.html.twig', $array);
            /**
             * L'utilisateur n'a pas les droits admin, render le template d'accès refusé.
             */
            }else{
                return $this->render('error/error_403.html.twig');
            }
        /**
         * L'utilisateur n'est pas connecté, redirigé vers le portail de connexion.
         */
        }else{
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Méthode appelée dans la celle ci-dessus lors de la soumission d'un nouvel utilisateur
     */
    public function newUser()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User($_POST['fName'],$_POST['sName'],$_POST['email'],$_POST['function'],$_POST['phone'],$this->setAdmin());
        /**
         * Password par défaut pour les nouveaux users haché avec bcrypt et salé aléatoirement.
         */
        $user->setPass(password_hash('TF3_@UD1T-2018', PASSWORD_BCRYPT));
        /**
         * L'EntityManager de Doctrine s'occupe de faire la conversion objet->base de données relationnelle.
         */
        $entityManager->persist($user);
        $entityManager->flush();
    }


    /**
     * Appelée par requête AJAX pour la suppression d'utilisateurs sur la page d'administration des utilisateurs.
     * La requête envoie l'ID par POST de chaque utilisateur coché dans la table.
     * La méthode boucle sur cette variable pour les récupérer et supprime l'utilisateur.
     *
     * @Route("/supprimer-utilisateur", name="delete_user", methods="POST")
     */
    public function deleteUser()
    {
        if(isset($_POST)) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($_POST as $key => $value) {
                $object = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $value]);
                $entityManager->remove($object);
            }
            $entityManager->flush();
        }
        return new Response('Utilisateur supprimé');
    }

    /**
     * Vérife si Administrateur a été coché dans le formulaire de création.
     */
    public function setAdmin()
    {
        if(isset($_POST['admin'])){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Méthode qui retourne la page profile et la modification des informations de l'utilisateur si il a soumis le formulaire de la page profile.
     *
     * @Route("/profile", name="view_profile_user")
     */
    public function viewProfile()
    {
        if(isset($_SESSION)){
            /**
             * Si le formulaire a été soumis.
             */
            if ($_POST) {
                $entityManager = $this->getDoctrine()->getManager();
                /**
                 * Récupère l'utilisateur
                 */
                $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $_SESSION['user']->id]);
                /**
                 * Le met à jour avec les informations du formulaire et UPDATE
                 */
                $user->updateProfile();
                $entityManager->persist($user);
                $entityManager->flush();
                $array = $_SESSION['user']->getAll();
            }else {
                $array = $_SESSION['user']->getAll();
            }
            return $this->render('user/profile_user.html.twig', $array);
        }else{
            return $this->redirectToRoute('homepage');
        }
    }


}
