<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 29/03/2018
 * Time: 15:42
 */

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Mailer;
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
                return $this->render('login.html.twig');
            }else{
                return $this->render('login.html.twig');
            }
        }
        elseif(isset($_SESSION['user'])){
            $array = $_SESSION['user']->getAll();
            return $this->render('homepage.html.twig',$array);
        }
        elseif(isset($_POST['user']) && isset($_POST['pass'])){
            return $this->userConnection();
        }
        else{
            return $this->render('login.html.twig');
        }
    }

    /**
     * @Route("/connexion", name="user_connection", methods="POST")
     */
    public function userConnection()
    {
        //$entityManager = $this->getDoctrine()->getManager();
        $email = $_POST['user'];
        $repositoryU = $this->getDoctrine()->getRepository(User::class);
        $user = $repositoryU->findOneBy(['email' => $email]);

        if ($user && $user->checkPassword($_POST['pass'],$email)){
            $user->startConnection();

            $array = $_SESSION['user']->getAll();
            $repositoryC = $this->getDoctrine()->getRepository(Company::class);
            $array['nbComp'] = $repositoryC->getNbRows();
            $array['nbUser'] = $repositoryU->getNbRows();

            return $this->render('homepage.html.twig',$array);
        }else{

            return $this->render('login.html.twig',['title'=>"Bienvenue",'error'=>'Identifiants invalides']);
        }
    }

    /**
     * @Route("/créer-utilisateur", name="create_user", methods="POST", options={"utf8": true})
     */
    public function newUser()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User($_POST['fName'],$_POST['sName'],$_POST['email'],$_POST['function'],$_POST['phone']);

        $randomPass = $user->generatePassword(10);
        $user->saltPassword($randomPass);

        $entityManager->persist($user);
        $entityManager->flush();

        $mailer = new Mailer($_POST['email'],'Test',$this->render('emails/new_user.html.twig'));
        $mailer->sendMailNewUSer();
        $json['content'] = 'Utilisateur créé';
        return new JsonResponse($json);
    }

    /**
     * @Route("/deconnexion", name="user_logoff", methods="GET")
     */
    public function userLogoff()
    {
       $_SESSION['user']->logoff();
       return $this->render('login.html.twig');
    }

    /**
     * @Route("/profile", name="view_profile_user")
     */
    public function viewProfile()
    {
        if(isset($_GET['save'])){
            $entityManager = $this->getDoctrine()->getManager();
            $object = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $_SESSION['user']->id]);
            $object->updateProfile();
            $entityManager->persist($object);
            $entityManager->flush();
            $array = $_SESSION['user']->getAll();

            return $this->render('profile_user.html.twig', $array);
        }
        else {
            $array = $_SESSION['user']->getAll();
            return $this->render('profile_user.html.twig', $array);
        }
    }

    /**
     * @Route("/supprimer-utilisateur", name="delete_user")
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

