<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 29/03/2018
 * Time: 15:42
 */

namespace App\Controller;

use App\Entity\Company;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;


class UserController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        //session_destroy();
        if (isset($_GET['rq'])){
            if(isset($_SESSION['user'])){
                session_destroy();
                return $this->render('user/login.html.twig');
            }else{
                return $this->render('user/login.html.twig');
            }
        }
        elseif(isset($_SESSION['user'])){
            $array = $_SESSION['user']->getAll();
            return $this->render('user/homepage.html.twig',$array);
        }
        elseif(isset($_POST['user']) && isset($_POST['pass'])){
            return $this->userConnection();
        }
        else{
            return $this->render('user/login.html.twig');
        }
    }


    /**
     * @Route("/administration-utilisateur", name="admin_user")
     */
    public function viewAdminUser()
    {
        if(isset($_SESSION)){
            if ($_SESSION['user']->isAdmin) {
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
            }else{
                return $this->render('error/error_403.html.twig');
            }
        }else{
            return $this->redirectToRoute('homepage');
        }
    }

    public function userConnection()
    {
        //$entityManager = $this->getDoctrine()->getManager();
        $email = $_POST['user'];
        // Ma clé privée
        $secret = "6Ld3lw4UAAAAAOfAaZ8SKgLfUbcMGx0fL8vRZbWp";
        // Paramètre renvoyé par le recaptcha
        $response = $_POST['g-recaptcha-response'];
        // On récupère l'IP de l'utilisateur
        $remoteip = $_SERVER['REMOTE_ADDR'];

        $api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
            . $secret
            . "&response=" . $response
            . "&remoteip=" . $remoteip ;

        $decode = json_decode(file_get_contents($api_url), true);


        $repositoryU = $this->getDoctrine()->getRepository(User::class);
        $user = $repositoryU->findOneBy(['email' => $email]);


        if($decode['success'] == true) {
            if ($user && password_verify($_POST['pass'], $user->getPass())) {
                $user->startConnection();
                $array = $_SESSION['user']->getAll();
                return $this->render('user/homepage.html.twig', $array);
            } else {
                return $this->render('user/login.html.twig', ['title' => "Bienvenue", 'error' => 'Identifiants invalides']);
            }
        }else{
            return $this->render('user/login.html.twig', ['title' => "Bienvenue", 'error' => 'Erreur de captcha']);
        }
    }

    public function newUser()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User($_POST['fName'],$_POST['sName'],$_POST['email'],$_POST['function'],$_POST['phone'],$this->setAdmin());

        $user->setPass(password_hash('TFE_AUDIT_2018', PASSWORD_BCRYPT));
        $entityManager->persist($user);
        $entityManager->flush();
    }

    public function setAdmin()
    {
        if(isset($_POST['admin'])){
            return true;
        }else{
            return false;
        }
    }


    /**
     * @Route("/profile", name="view_profile_user")
     */
    public function viewProfile()
    {
        if(isset($_SESSION)) {
            if ($_POST) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $_SESSION['user']->id]);
                $user->updateProfile();
                $entityManager->persist($user);
                $entityManager->flush();
                $array = $_SESSION['user']->getAll();
            } else {
                $array = $_SESSION['user']->getAll();
            }
            return $this->render('user/profile_user.html.twig', $array);
        }else{
            return $this->redirectToRoute('homepage');
        }
    }

    /*
     * JQUERY
     */
    /**
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
        return new Response('ok');
    }
}

