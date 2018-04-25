<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 18/04/2018
 * Time: 17:16
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;



class AdministrationController extends AbstractController
{

    /**
     * @Route("/administration-utilisateur", name="admin_user")
     */
    public function viewAdminUser()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $array = $_SESSION['user']->getAll();
        $array['users'] = $repository->findAll();
        return $this->render('administration_user.html.twig',$array);

    }

}