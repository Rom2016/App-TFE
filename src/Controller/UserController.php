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

        $file = "login_form.html";
        return new Response(file_get_contents($file));
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
            $json=[];
            $json['content'] = $this->render('homepage.html.twig');
            return new JsonResponse($json);
        }else{
            $json=['loginError'=>'<p>Identifiants invalides</p>'];
            return new JsonResponse($json);
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

