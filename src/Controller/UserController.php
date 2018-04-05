<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 29/03/2018
 * Time: 15:42
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;


class UserController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods="GET")
     */
    public function loginpage()
    {

        if(isset($_SESSION['user'])){
            return $this->render('homepage.html.twig',['title'=>'Accueil']);
        }else{
            return $this->render('login.html.twig',['title'=>'Bienvenue']);
        }

    }

    /**
     * @Route("/connexion", name="user_connection", methods="POST")
     */
    public function userConnection()
    {
        //$entityManager = $this->getDoctrine()->getManager();
        $email = $_POST['user'];
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(['email' => $email]);

        if ($user && $user->checkPassword($_POST['pass'],$email)){
            $user->startConnection();
            return $this->render('homepage.html.twig',['title'=>'Accueil']);
        }else{
            return $this->render('login.html.twig',['title'=>"Bienvenue",'error'=>'Identifiants invalides']);
        }
    }

    /**
     * @Route("/creer-utilisateur", name="create_user", methods="POST")
     */
    public function newUser()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $pass = md5($_POST['pass'].$_POST['user']);
        $user = new User($_POST['fName'],$_POST['sName'],$pass,$_POST['email']);

        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * @Route("/deconnexion", name="user_logoff", methods="GET")
     */
    public function userLogoff()
    {
        $_SESSION['user']->logoff();
        $this->homepage();
    }

}

