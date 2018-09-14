<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 14/09/2018
 * Time: 22:33
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\AppUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdministrationController extends AbstractController
{

    /**
     * Méthode qui gère la partie administration des utilisateurs
     *
     * @Route("/administration", name="admin")
     */
    public function viewAdmin()
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
                    //$this->newUser();
                    break;
            }
        }
        $repository = $this->getDoctrine()->getRepository(AppUser::class);
        $array['users'] = $repository->findAll();
        return $this->render('administration/administration.html.twig', $array);
        /**
         * L'utilisateur n'a pas les droits admin, render le template d'accès refusé.
         */
        /**
         * L'utilisateur n'est pas connecté, redirigé vers le portail de connexion.
         */
    }

}