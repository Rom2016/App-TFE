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

class AccueilController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function Bienvenue()
    {

        $file = "login_form.html";
        return new Response(file_get_contents($file));
    }


    /**
     * @Route("/connexion", name="connexion_utilisateur", methods="POST")
     */
    public function connexionUtilisateur(){
        return new JsonResponse(['result'=>'ok']);
    }
}
