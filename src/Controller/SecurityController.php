<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 06/01/2019
 * Time: 18:30
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class SecurityController extends AbstractController
{
    /**
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
     * @Route("/administration/connexion", name="login_admin")
     */
    public function loginAdmin(AuthenticationUtils $authenticationUtils)
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


}